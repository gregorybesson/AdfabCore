<?php
namespace AdfabCore\Service;

use AdfabCore\Entity;
use AdfabCore\Exception;
use AdfabCore\Mapper;
use AdfabCore\Service\Registry;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use ZfcBase\EventManager\EventProvider;

/**
 * main Cron class
 *
 * handle cron job registration, validation, scheduling, running, and cleanup
 *
 * @author heartsentwined <heartsentwined@cogito-lab.com>
 * @license GPL http://opensource.org/licenses/gpl-license.php
 */
class Cron extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * how long ahead to schedule cron jobs
     *
     * @var int (minute)
     */
    protected $scheduleAhead = 60;

    /**
     * the Doctrine ORM Entity Manager
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * List of cron jobs to be managed
     */
    protected $cronjobs;

    /**
     * how long before a scheduled job is considered missed
     *
     * @var int (minute)
     */
    protected $scheduleLifetime = 60;

    /**
     * maximum running time of each cron job
     *
     * @var int (minute)
     */
    protected $maxRunningTime = 60;

    /**
     * how long to keep successfully completed cron job logs
     *
     * @var int (minute)
     */
    protected $successLogLifetime = 300;

    /**
     * how long to keep failed (missed / error) cron job logs
     *
     * @var int (minute)
     */
    protected $failureLogLifetime = 10080;

    /**
     * set of pending cron jobs
     *
     * wrapped the Repo function here to implement a (crude) cache feature
     *
     * @var array of Entity\Cronjob
     */
    protected $pending;

    /**
     * main entry function
     *
     * 1. schedule new cron jobs
     * 2. process cron jobs
     * 3. cleanup old logs
     *
     * @return self
     */
    public function run()
    {
        $this->schedule()
        ->process()
        ->cleanup();

        return $this;
    }

    /**
     * trigger an event and fetch crons
     * Return array
     */
    public function getCronjobs()
    {
        if (!$this->cronjobs) {
            $cronjobs = array();

            $results = $this->getServiceManager()
                ->get('application')
                ->getEventManager()
                ->trigger(__FUNCTION__, $this, array(
                        'cronjobs' => $cronjobs
                ));

            if ($results) {
                foreach ($results as $key => $cron) {
                    foreach ($cron as $id => $conf) {
                        $cronjobs[$id] = $conf;
                    }
                }
            }

            $this->setCronjobs($cronjobs);
        }

        return $this->cronjobs;
    }

    /**
     *
     */
    public function setCronjobs($cronjobs)
    {
        $this->cronjobs = $cronjobs;

        return $this;
    }

    public function getPending()
    {
        if (!$this->pending) {
            $this->pending = $this->getEm()
                ->getRepository('AdfabCore\Entity\Cronjob')
                ->getPending();
        }

        return $this->pending;
    }

    public function resetPending()
    {
        $this->pending = null;

        return $this;
    }

    /**
     * run cron jobs
     *
     * @return self
     */
    public function process()
    {
        $em = $this->getEm();
        $cronRegistry = $this->getCronjobs();
        $pending = $this->getPending();
        $scheduleLifetime = $this->scheduleLifetime * 60; //convert min to sec

        $now = new \DateTime;
        foreach ($pending as $job) {
            $scheduleTime = $job->getScheduleTime();

            if ($scheduleTime > $now) {
                continue;
            }

            try {
                $errorStatus = Mapper\Cronjob::STATUS_ERROR;

                $missedTime = clone $now;
                $timestamp = $missedTime->getTimestamp();
                $timestamp -= $scheduleLifetime;
                $missedTime->setTimestamp($timestamp);

                if ($scheduleTime < $missedTime) {
                    $errorStatus = Mapper\Cronjob::STATUS_MISSED;
                    throw new Exception\RuntimeException(
                        'too late for job'
                    );
                }

                $code = $job->getCode();

                if (!isset($cronRegistry[$code])) {
                    throw new Exception\RuntimeException(sprintf(
                        'job "%s" undefined in cron registry',
                        $code
                    ));
                }

                if (!$this->tryLockJob($job)) {
                    //another cron started this job intermittently. skip.
                    continue;
                }

                //run job now
                $callback = $cronRegistry[$code]['callback'];
                $args = $cronRegistry[$code]['args'];

                $job->setExecuteTime(new \DateTime);
                $em->persist($job);
                $em->flush();

                call_user_func_array($callback, $args);

                $job
                    ->setStatus(Mapper\Cronjob::STATUS_SUCCESS)
                    ->setFinishTime(new \DateTime);

            } catch (\Exception $e) {
                $job
                    ->setStatus($errorStatus)
                    ->setErrorMsg($e->getMessage())
                    ->setStackTrace($e->getTraceAsString());
            }

            $em->persist($job);
            $em->flush();
        }

        return $this;
    }

    /**
     * schedule cron jobs
     *
     * @return self
     */
    public function schedule()
    {
        $em = $this->getEm();
        $pending = $this->getPending();
        $exists = array();
        foreach ($pending as $job) {
            $identifier = $job->getCode();
            $identifier .= $job->getScheduleTime()->getTimeStamp();
            $exists[$identifier] = true;
        }

        $scheduleAhead = $this->getScheduleAhead() * 60;

        $cronRegistry = $this->getCronjobs();

        foreach ($cronRegistry as $code => $item) {
            $now = time();
            $timeAhead = $now + $scheduleAhead;

            for ($time = $now; $time < $timeAhead; $time += 60) {
                $scheduleTime = new \DateTime();
                $scheduleTime->setTimestamp($time);
                $scheduleTime->setTime(
                    $scheduleTime->format('H'),
                    $scheduleTime->format('i')
                );
                $scheduleTimestamp = $scheduleTime->getTimestamp();

                $identifier = $code . $scheduleTimestamp;
                if (isset($exists[$identifier])) {
                    //already scheduled
                    continue;
                }

                $job = new Entity\Cronjob;
                if ($this->matchTime(
                    $scheduleTimestamp, $item['frequency'])) {
                    $job
                        ->setCode($code)
                        ->setStatus(Mapper\Cronjob::STATUS_PENDING)
                        ->setCreateTime(new \DateTime)
                        ->setScheduleTime($scheduleTime);
                    $em->persist($job);
                    $exists[$identifier] = true;
                }
            }
        }

        $em->flush();

        return $this;
    }

    /**
     * perform various cleanup work
     *
     * @return self
     */
    public function cleanup()
    {
        $this
            ->recoverRunning()
            ->cleanLog();

        return $this;
    }

    public function recoverRunning()
    {
        $em = $this->getEm();
        $running = $em->getRepository('AdfabCore\Entity\Cronjob')
            ->getRunning();
        $expiryTime = time() - $this->getMaxRunningTime() * 60;

        foreach ($running as $job) {
            if ($job->getExecuteTime()
                && $job->getExecuteTime()->getTimestamp() < $expiryTime) {
                $job
                    ->setStatus(Mapper\Cronjob::STATUS_PENDING)
                    ->setErrorMsg(null)
                    ->setStackTrace(null)
                    ->setScheduleTime(new \DateTime)
                    ->setExecuteTime(null);
            }
        }

        $this->getEm()->flush();

        return $this;
    }

    /**
     * delete old cron job logs
     *
     * @return self
     */
    public function cleanLog()
    {
        $em = $this->getEm();
        $lifetime = array(
            Mapper\Cronjob::STATUS_SUCCESS  =>
                $this->getSuccessLogLifetime() * 60,
            Mapper\Cronjob::STATUS_MISSED   =>
                $this->getFailureLogLifetime() * 60,
            Mapper\Cronjob::STATUS_ERROR    =>
                $this->getFailureLogLifetime() * 60,
        );

        $history = $em->getRepository('AdfabCore\Entity\Cronjob')
            ->getHistory();

        $now = time();
        foreach ($history as $job) {
            if ($job->getExecuteTime()
                && $job->getExecuteTime()->getTimestamp()
                    < $now - $lifetime[$job->getStatus()]) {
                $em->remove($job);
            }
        }

        $em->flush();

        return $this;
    }

    /**
     * wrapper function
     * @see Registry::register()
     */
    public static function register(
        $code, $frequency, $callback, array $args = array())
    {
        Registry::register($code, $frequency, $callback, $args);
    }

    /**
     * try to acquire a lock on a cron job
     *
     * set a job to 'running' only if it is currently 'pending'
     *
     * @param  Entity\Cronjob $job
     * @return bool
     */
    public function tryLockJob(Entity\Cronjob $job)
    {
        $em = $this->getEm();
        $repo = $em->getRepository('AdfabCore\Entity\Cronjob');
        if ($job->getStatus() === Mapper\Cronjob::STATUS_PENDING) {
            $job->setStatus(Mapper\Cronjob::STATUS_RUNNING);
            $em->persist($job);
            $em->flush();

            // flush() succeeded if reached here;
            // otherwise an Exception would have been thrown
            return true;
        }

        return false;
    }

    /**
     * determine whether a given time falls within the given cron expr
     *
     * @param string|numeric $time
     *      timestamp or strtotime()-compatible string
     * @param string $expr
     *      any valid cron expression, in addition supporting:
     *      range: '0-5'
     *      range + interval: '10-59/5'
     *      comma-separated combinations of these: '1,4,7,10-20'
     *      English months: 'january'
     *      English months (abbreviated to three letters): 'jan'
     *      English weekdays: 'monday'
     *      English weekdays (abbreviated to three letters): 'mon'
     *      These text counterparts can be used in all places where their
     *          numerical counterparts are allowed, e.g. 'jan-jun/2'
     *      A full example:
     *          '0-5,10-59/5 * 2-10,15-25 january-june/2 mon-fri' -
     *          every minute between minute 0-5 + every 5th min between 10-59
     *          every hour
     *          every day between day 2-10 and day 15-25
     *          every 2nd month between January-June
     *          Monday-Friday
     * @throws Exception\InvalidArgumentException on invalid cron expression
     * @return bool
     */
    public static function matchTime($time, $expr)
    {
        //ArgValidator::assert($time, array('string', 'numeric'));
        //ArgValidator::assert($expr, 'string');

        $cronExpr = preg_split('/\s+/', $expr, null, PREG_SPLIT_NO_EMPTY);
        if (count($cronExpr) !== 5) {
            throw new Exception\InvalidArgumentException(sprintf(
                    'cron expression should have exactly 5 arguments, "%s" given',
                    $expr
            ));
        }

        if (is_string($time)) $time = strtotime($time);

        $date = getdate($time);

        return self::matchTimeComponent($cronExpr[0], $date['minutes'])
        && self::matchTimeComponent($cronExpr[1], $date['hours'])
        && self::matchTimeComponent($cronExpr[2], $date['mday'])
        && self::matchTimeComponent($cronExpr[3], $date['mon'])
        && self::matchTimeComponent($cronExpr[4], $date['wday']);
    }

    /**
     * match a cron expression component to a given corresponding date/time
     *
     * In the expression, * * * * *, each component
     *      *[1] *[2] *[3] *[4] *[5]
     * will correspond to a getdate() component
     * 1. $date['minutes']
     * 2. $date['hours']
     * 3. $date['mday']
     * 4. $date['mon']
     * 5. $date['wday']
     *
     * @see self::exprToNumeric() for additional valid string values
     *
     * @param  string                             $expr
     * @param  numeric                            $num
     * @throws Exception\InvalidArgumentException on invalid expression
     * @return bool
     */
    public static function matchTimeComponent($expr, $num)
    {
        //ArgValidator::assert($expr, 'string');
        //ArgValidator::assert($num, 'numeric');

        //handle all match
        if ($expr === '*') {
            return true;
        }

        //handle multiple options
        if (strpos($expr, ',') !== false) {
            $args = explode(',', $expr);
            foreach ($args as $arg) {
                if (self::matchTimeComponent($arg, $num)) {
                    return true;
                }
            }

            return false;
        }

        //handle modulus
        if (strpos($expr, '/') !== false) {
            $arg = explode('/', $expr);
            if (count($arg) !== 2) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'invalid cron expression component: '
                        . 'expecting match/modulus, "%s" given',
                        $expr
                ));
            }
            if (!is_numeric($arg[1])) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'invalid cron expression component: '
                        . 'expecting numeric modulus, "%s" given',
                        $expr
                ));
            }

            $expr = $arg[0];
            $mod = $arg[1];
        } else {
            $mod = 1;
        }

        //handle all match by modulus
        if ($expr === '*') {
            $from = 0;
            $to   = 60;
        }
        //handle range
        elseif (strpos($expr, '-') !== false) {
            $arg = explode('-', $expr);
            if (count($arg) !== 2) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'invalid cron expression component: '
                        . 'expecting from-to structure, "%s" given',
                        $expr
                ));
            }
            $from = self::exprToNumeric($arg[0]);
            $to = self::exprToNumeric($arg[1]);
        }
        //handle regular token
        else {
            $from = self::exprToNumeric($expr);
            $to = $from;
        }

        if ($from === false || $to === false) {
            throw new Exception\InvalidArgumentException(sprintf(
                    'invalid cron expression component: '
                    . 'expecting numeric or valid string, "%s" given',
                    $expr
            ));
        }

        return ($num >= $from) && ($num <= $to) && ($num % $mod === 0);
    }

    /**
     * parse a string month / weekday expression to its numeric equivalent
     *
     * @param string|numeric $value
     *      accepts, case insensitive,
     *      - Jan - Dec
     *      - Sun - Sat
     *      - (or their long forms - only the first three letters important)
     * @return int|false
     */
    public static function exprToNumeric($value)
    {
        //ArgValidator::assert($value, array('string', 'numeric'));

        static $data = array(
                'jan'   => 1,
                'feb'   => 2,
                'mar'   => 3,
                'apr'   => 4,
                'may'   => 5,
                'jun'   => 6,
                'jul'   => 7,
                'aug'   => 8,
                'sep'   => 9,
                'oct'   => 10,
                'nov'   => 11,
                'dec'   => 12,

                'sun'   => 0,
                'mon'   => 1,
                'tue'   => 2,
                'wed'   => 3,
                'thu'   => 4,
                'fri'   => 5,
                'sat'   => 6,
        );

        if (is_numeric($value)) {
            if (in_array((int) $value, $data, true)) {
                return $value;
            } else {
                return false;
            }
        }

        if (is_string($value)) {
            $value = strtolower(substr($value, 0, 3));
            if (isset($data[$value])) {
                return $data[$value];
            }
        }

        return false;
    }

    public function setScheduleAhead($scheduleAhead)
    {
        $this->scheduleAhead = $scheduleAhead;

        return $this;
    }

    public function getScheduleAhead()
    {
        return $this->scheduleAhead;
    }

    public function setScheduleLifetime($scheduleLifetime)
    {
        $this->scheduleLifetime = $scheduleLifetime;

        return $this;
    }

    public function getScheduleLifeTime()
    {
        return $this->scheduleLifeTime;
    }

    public function setMaxRunningTime($maxRunningTime)
    {
        $this->maxRunningTime = $maxRunningTime;

        return $this;
    }

    public function getMaxRunningtime()
    {
        return $this->maxRunningTime;
    }

    public function setSuccessLogLifetime($successLogLifetime)
    {
        $this->successLogLifetime = $successLogLifetime;

        return $this;
    }
    public function getSuccessLogLifetime()
    {
        return $this->successLogLifetime;
    }

    public function setFailureLogLifetime($failureLogLifetime)
    {
        $this->failureLogLifetime = $failureLogLifetime;

        return $this;
    }

    public function getFailureLogLifetime()
    {
        return $this->failureLogLifetime;
    }

    public function setEm(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    public function getEm()
    {
        if (!$this->em) {
            $this->setEm($this->getServiceManager()->get('adfabcore_doctrine_em'));
        }

        return $this->em;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}

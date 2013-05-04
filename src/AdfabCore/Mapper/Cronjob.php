<?php

namespace AdfabCore\Mapper;

use Doctrine\ORM\EntityRepository;

class Cronjob extends EntityRepository
{
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_SUCCESS = 'success';
    const STATUS_MISSED  = 'missed';
    const STATUS_ERROR   = 'error';

    /**
     * get pending cron jobs
     *
     * @return array of \Heartsentwined\Cron\Entity\Job
     */
    public function getPending()
    {
        $dqb = $this->_em->createQueryBuilder();
        $dqb->select(array('j'))
            ->from('AdfabCore\Entity\Cronjob', 'j')
            ->where($dqb->expr()->in('j.status', array(self::STATUS_PENDING)))
            ->orderBy('j.scheduleTime', 'ASC');

        return $dqb->getQuery()->getResult();
    }

    /**
     * get running cron jobs
     *
     * @return array of \AdfabCore\Entity\Job
     */
    public function getRunning()
    {
        $dqb = $this->_em->createQueryBuilder();
        $dqb->select(array('j'))
            ->from('AdfabCore\Entity\Cronjob', 'j')
            ->where($dqb->expr()->in('j.status', array(self::STATUS_RUNNING)))
            ->orderBy('j.scheduleTime', 'ASC');

        return $dqb->getQuery()->getResult();
    }

    /**
     * get completed cron jobs
     *
     * @return array of \AdfabCore\Entity\Job
     */
    public function getHistory()
    {
        $dqb = $this->_em->createQueryBuilder();
        $dqb->select(array('j'))
            ->from('AdfabCore\Entity\Cronjob', 'j')
            ->where($dqb->expr()->in('j.status', array(
                self::STATUS_SUCCESS, self::STATUS_MISSED, self::STATUS_ERROR,
            )))
            ->orderBy('j.scheduleTime', 'ASC');

        return $dqb->getQuery()->getResult();
    }
}

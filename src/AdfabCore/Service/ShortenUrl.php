<?php
namespace AdfabCore\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use ZfcBase\EventManager\EventProvider;

/**
 * main class
 */
class ShortenUrl extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * This method call Bit.ly to shorten a given URL.
     * @param  unknown_type $url
     * @return unknown
     */
    public function shortenUrl($url)
    {
        $client = new \Zend\Http\Client($this->getOptions()->getBitlyUrl());
        $client->setParameterGet(array(
            'format'  => 'json',
            'longUrl' => $url,
            'login'   => $this->getOptions()->getBitlyUsername(),
            'apiKey'  => $this->getOptions()->getBitlyApiKey(),
        ));

        $result = $client->send();
        if ($result) {
            $jsonResult = \Zend\Json\Json::decode($result->getBody());
            if ($jsonResult->status_code == 200) {
                return $jsonResult->data->url;
            }
        }

        return $url;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions($this->getServiceManager()->get('adfabcore_module_options'));
        }

        return $this->options;
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

<?php

namespace AdfabCore\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ShortenUrl extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;

    /**
     * @var service
     */
    protected $service;

    /**
     * Returns a shortened Url via bit.ly
     *
     * @param string $longUrl
     */
    public function shortenUrl($longUrl)
    {
        return $this->getService()->shortenUrl($longUrl);
    }

    /**
     * set service
     *
     * @param  $service
     * @return String
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * get mapper
     *
     * @return Service
     */
    public function getService()
    {
        if (!$this->service) {
            $this->setService($this->getServiceLocator()->get('adfabcore_shortenurl_service'));
        }

        return $this->service;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}

<?php

namespace AdfabCore\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    protected $bitlyUsername = 'o_7t2s2bjmun';
    protected $bitlyApiKey   = 'R_335290ffb3f5fc08b45d3e0e6678c3db';
    protected $bitlyUrl      = 'http://api.bit.ly/v3/shorten';
    protected $transport_class = 'Zend\Mail\Transport\File';
    protected $options_class   = 'Zend\Mail\Transport\FileOptions';
    protected $options   = array('path' => 'data/mail/');
    protected $quConfig = array();
    protected $googleAnalytics = array('id' => '');
    protected $adServing = array();
    protected $defaultShareMessage = 'Venez jouer';
    

    protected $ckeditor = array();
    
    public function getDefaultShareMessage()
    {
        return $this->defaultShareMessage;
    }
    
    public function setDefaultShareMessage($defaultShareMessage)
    {
        $this->defaultShareMessage = $defaultShareMessage;
    
        return $this;
    }

    public function getAdServing()
    {
        return $this->adServing;
    }
    
    public function setAdServing($adServing)
    {
        $this->adServing = $adServing;
    
        return $this;
    }
    
    public function getGoogleAnalytics()
    {
        return $this->googleAnalytics;
    }

    public function setGoogleAnalytics($googleAnalytics)
    {
        $this->googleAnalytics = $googleAnalytics;

        return $this;
    }

    public function getQuConfig()
    {
        return $this->quConfig;
    }

    public function setQuConfig($quConfig)
    {
        $this->quConfig = $quConfig;

        return $this;
    }

    public function getCkeditor()
    {
        return $this->ckeditor;
    }

    public function setCkeditor($ckeditor)
    {
        $this->ckeditor = $ckeditor;

        return $this;
    }

    public function getTransportClass()
    {
        return $this->transport_class;
    }

    public function setTransportClass($transport_class)
    {
        $this->transport_class = $transport_class;

        return $this;
    }

    public function getOptionsClass()
    {
        return $this->options_class;
    }

    public function setOptionsClass($options_class)
    {
        $this->options_class = $options_class;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getBitlyUsername()
    {
        return $this->bitlyUsername;
    }

    public function setBitlyUsername($bitlyUsername)
    {
        $this->bitlyUsername = $bitlyUsername;

        return $this;
    }

    public function getBitlyApiKey()
    {
        return $this->bitlyApiKey;
    }

    public function setBitlyApiKey($bitlyApiKey)
    {
        $this->bitlyApiKey = $bitlyApiKey;

        return $this;
    }

    public function getBitlyUrl()
    {
        return $this->bitlyUrl;
    }

    public function setBitlyUrl($bitlyUrl)
    {
        $this->bitlyUrl = $bitlyUrl;

        return $this;
    }
}

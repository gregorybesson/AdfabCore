<?php
/**
 * @Author: Cel Ticó Petit
 * @Contact: cel@cenics.net
 * @Company: Cencis s.c.p.
 */
namespace AdfabCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ElfinderController extends AbstractActionController
{

    protected $Config;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel();
        $this->getConfig();
        $view->BasePath = $this->Config['BasePath'];
        $view->ConnectorPath = '/elfinder/connector';

        return $view;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function ckeditorAction()
    {
        $view = new ViewModel();
        $this->getConfig();
        $view->BasePath    = $this->Config['BasePath'];
        $view->ConnectorPath = '/elfinder/connector';
        $view->setTerminal(true);

        return $view;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function connectorAction()
    {
        $view = new ViewModel();
        $this->getConfig();

        $root = array();
        if(isset($this->Config['QuRoots'])){
            $root = $this->Config['QuRoots'];
        }
        
        $opts = array(
            'debug' => false,
            'roots' => array($root)
        );
        
        $connector = new \elFinderConnector(new \elFinder($opts));
        $connector->run();

        $view->setTerminal(true);

        return $view;
    }

    /**
     * @param $attr
     * @param $path
     * @param $data
     * @param $volume
     *
     * @return bool|null
     */
    public function access($attr, $path, $data, $volume)
    {
        return strpos(basename($path), '.') === 0
            ? !($attr == 'read' || $attr == 'write')
            :  null;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        if (!$this->Config) {
            $config       = $this->getServiceLocator()->get('config');
            if(isset($config['adfabcore']) && isset($config['adfabcore']['QuConfig']) && isset($config['adfabcore']['QuConfig']['QuElFinder'])){
                $this->Config = $config['adfabcore']['QuConfig']['QuElFinder'];
            } else {
                $this->Config = array();
            }
        }

        return $this->Config;
    }

}

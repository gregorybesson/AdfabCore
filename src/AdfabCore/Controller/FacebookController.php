<?php
namespace AdfabCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FacebookController extends AbstractActionController
{

    protected $Config;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $viewModel = new ViewModel();
        //$viewModel->setTerminal(true);

        $this->getConfig();

        return $viewModel;
    }

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function onglet1Action()
    {
        $viewModel = new ViewModel();

        return $viewModel;
    }

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function onglet2Action()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        return $viewModel;
    }

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function onglet3Action()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        return $viewModel;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        if (!$this->Config) {
            $config       = $this->getServiceLocator()->get('config');
            $this->Config = $config['adfabcore']['QuConfig']['QuElFinder'];
        }

        return $this->Config;
    }

}

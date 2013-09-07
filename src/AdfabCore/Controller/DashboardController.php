<?php

namespace AdfabCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController
{
    public function indexAction()
    {
    	return $this->forward()->dispatch('adminstats', array('action' => 'index'));
    	//return new ViewModel();
    }
}

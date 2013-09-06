<?php
namespace AdfabCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

class FormgenController extends AbstractActionController
{
    public function indexAction()
    {
        return array ();
    }

    public function viewAction()
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $headScript = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadScript');
        $headScript->appendFile ( $renderer->adminAssetPath('js/form/parse.form.js'));

        $formId = $this->params('form');

        return array('form_id' => $formId);
    }

    public function testAction()
    {
        $form = new AddUser();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = new User();
            $formValidator = new AddUserValidator();

            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
            }
            echo '<pre>'; var_dump($form->getData()); echo '</pre>';
        }

        return array('form' => $form);
    }

    public function createAction()
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $headScript = $this->getServiceLocator()->get('viewhelpermanager')->get('HeadScript');
        $headScript->appendFile ( $renderer->adminAssetPath('js/form/create.form.js'));
        $headScript->appendFile ( $renderer->adminAssetPath('js/form/line.text.js'));
        $headScript->appendFile ( $renderer->adminAssetPath('js/form/add.form.js'));
        $headScript->appendFile ( $renderer->adminAssetPath('js/form/json.form.js'));
        $headScript->appendFile ( $renderer->adminAssetPath('js/form/edit.form.js'));

        return array();
    }

    public function inputAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function passwordAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function passwordverifyAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function numberAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function phoneAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function paragraphAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function checkboxAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function radioAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function dropdownAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function emailAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function dateAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function uploadAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function creditcardAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function urlAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function hiddenAction()
    {
        $result = $this->getAjax();

        return $result;
    }

    public function getAjax()
    {
        $request = $this->getRequest ();
        $results = $request->getQuery ();

        $result = new ViewModel (array(
                'result' => $results,
        ));

        $result->setTerminal ( true );

        return $result;
    }
}

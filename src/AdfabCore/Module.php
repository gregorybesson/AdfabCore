<?php

namespace AdfabCore;

use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap($e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        /* Set the translator for default validation messages
         * I've copy/paste the Validator messages from ZF2 and placed them in a correct path : AdfabCore
        * TODO : Centraliser la trad pour les Helper et les Plugins
        */
        $translator = $serviceManager->get('translator');

        //Translation based on Browser's locale
        //$translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));

        // positionnement de la langue pour les traductions de date avec strftime
        setlocale(LC_TIME, "fr_FR", 'fr_FR.utf8', 'fra');

        AbstractValidator::setDefaultTranslator($translator,'adfabcore');

        /**
         * Adding a Filter to slugify a string (make it URL compliiant)
         */
        $filterChain = new \Zend\Filter\FilterChain();
        $filterChain->getPluginManager()->setInvokableClass(
            'slugify', 'AdfabCore\Filter\Slugify'
        );
        $filterChain->attach(new Filter\Slugify());

        // Start the session container
        $config = $e->getApplication()->getServiceManager()->get('config');

        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config['session']);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();
        
            // Design management : template and assets management
        if(isset($config['design'])){
        	$configHasChanged = false;
        	$viewResolverPathStack = $e->getApplication()->getServiceManager()->get('ViewTemplatePathStack');
        	if(isset($config['design']['admin']) && isset($config['design']['admin']['package']) && isset($config['design']['admin']['theme'])){
        		$adminPath = __DIR__ . '/../../../../../design/admin/'. $config['design']['admin']['package'] .'/'. $config['design']['admin']['theme'];
        		$pathStack = array($adminPath);
        		
        		// Assetic pour les CSS
        		$config['assetic_configuration']['modules']['admin']['root_path'][] = $adminPath . '/assets';
        		// Resolver des templates phtml
        		$viewResolverPathStack->addPaths($pathStack);
        		
        		$filename = $adminPath . '/assets.php';
        		if(is_file($filename) && is_readable($filename)){
        			$configAssets = new \Zend\Config\Config(include $filename);
        			$config = array_replace_recursive($config, $configAssets->toArray());
        			$configHasChanged = true;
        		}
        	}
        	if(isset($config['design']['frontend']) && isset($config['design']['frontend']['package']) && isset($config['design']['frontend']['theme'])){
        		$frontendPath = __DIR__ . '/../../../../../design/frontend/'. $config['design']['frontend']['package'] .'/'. $config['design']['frontend']['theme'];
        		$pathStack = array($frontendPath);
        		// Assetic pour les CSS
        		$config['assetic_configuration']['modules']['frontend']['root_path'][] = $frontendPath . '/assets';
        		$viewResolverPathStack->addPaths($pathStack);
        		
        		$filename = $frontendPath . '/assets.php';
        		if(is_file($filename) && is_readable($filename)){
        			$configAssets = new \Zend\Config\Config(include $filename);
        			$config = array_replace_recursive($configAssets->toArray(), $config);
        			$configHasChanged = true;
        		}
        	}
        	if($configHasChanged){
        		$e->getApplication()->getServiceManager()->setAllowOverride(true);
        		$e->getApplication()->getServiceManager()->setService('config', $config);
        	}
        }

        /**
         * Optional: If you later want to use namespaces, you can already store the
         * Manager in the shared (static) Container (=namespace) field
         */
        \Zend\Session\Container::setDefaultManager($sessionManager);

        // Google Analytics : When the render event is triggered, we invoke the view helper to
        // render the javascript code.
        $e->getApplication()->getEventManager()->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, function(\Zend\Mvc\MvcEvent $e) use ($serviceManager) {
            $view   = $serviceManager->get('ViewHelperManager');
            $plugin = $view->get('googleAnalytics');
            $plugin();
            
            $viewModel 		 = $e->getViewModel();
            $match			 = $e->getRouteMatch();
            $channel		 = isset($match)? $match->getParam('channel', ''):'';
            $viewModel->channel = $channel;
            foreach($viewModel->getChildren() as $child){
            	$child->channel = $channel;
            }
        });

        // Detect if the app is called from FB and store unencrypted signed_request
        $e->getApplication()->getEventManager()->attach("dispatch", function($e) {
       		$session = new Container('facebook');
       		$fb = $e->getRequest()->getPost()->get('signed_request');
       		if ($fb) {
       			list($encoded_sig, $payload) = explode('.', $fb, 2);
       			$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
       			$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        		$session->offsetSet('signed_request',  $data);
        	}
        },200);

        /**
         * This listener gives the possibility to select the layout on module / controller / action level !
         * Just configure it in any module config or autoloaded config.
         */
        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $config     = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['core_layout'])) {
                $controller      = $e->getTarget();
                $controllerClass = get_class($controller);
                $moduleName		 = substr($controllerClass, 0, strpos($controllerClass, '\\'));
                $match			 = $e->getRouteMatch();
                $controllerName  = $match->getParam('controller', 'not-found');
                $actionName 	 = $match->getParam('action', 'not-found');
                $channel		 = $match->getParam('channel', 'not-found');
                $viewModel 		 = $e->getViewModel();     

                //print_r($match);

                //die('module : '.$moduleName . "- controller : " . $controllerName . "- action :" . $actionName);

                /**
                 * Assign the correct layout
                 */
                
                if (isset($config['core_layout'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['channel'][$channel]['default_layout'])) {
                    //print_r($config['core_layout'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['channel'][$channel]['default_layout']);
                    $controller->layout($config['core_layout'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['channel'][$channel]['default_layout']);
                } elseif (isset($config['core_layout'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['default_layout'])) {
                    //print_r($config['core_layout'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['default_layout']);
                    $controller->layout($config['core_layout'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['default_layout']);
                } elseif (isset($config['core_layout'][$moduleName]['controllers'][$controllerName]['channel'][$channel]['default_layout'])) {
                    //print_r($config['core_layout'][$moduleName]['controllers'][$controllerName]['channel'][$channel]['default_layout']);
                    $controller->layout($config['core_layout'][$moduleName]['controllers'][$controllerName]['channel'][$channel]['default_layout']);
                } elseif (isset($config['core_layout'][$moduleName]['controllers'][$controllerName]['default_layout'])) {
                    //print_r($config['core_layout'][$moduleName]['controllers'][$controllerName]['default_layout']);
                    $controller->layout($config['core_layout'][$moduleName]['controllers'][$controllerName]['default_layout']);
                } elseif (isset($config['core_layout'][$moduleName]['channel'][$channel]['default_layout'])) {
                    //print_r($config['core_layout'][$moduleName]['channel'][$channel]['default_layout']);
                    $controller->layout($config['core_layout'][$moduleName]['channel'][$channel]['default_layout']);
                } elseif (isset($config['core_layout'][$moduleName]['default_layout'])) {
                    //print_r($config['core_layout'][$moduleName]['default_layout']);
                    $controller->layout($config['core_layout'][$moduleName]['default_layout']);
                }

                /**
                 * Create variables attached to layout containing path views
                 * cascading assignment is managed
                 */
                if (isset($config['core_layout'][$moduleName]['children_views'])) {
                    foreach ($config['core_layout'][$moduleName]['children_views'] as $k => $v) {
                        $viewModel->$k  = $v;
                    }
                }
                if (isset($config['core_layout'][$moduleName]['controllers'][$controllerName]['children_views'])) {
                    foreach ($config['core_layout'][$moduleName]['controllers'][$controllerName]['children_views'] as $k => $v) {
                        $viewModel->$k  = $v;
                    }
                }
                if (isset($config['core_layout'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['children_views'])) {
                    foreach ($config['core_layout'][$moduleName]['controllers'][$controllerName]['actions'][$actionName]['children_views'] as $k => $v) {
                        $viewModel->$k  = $v;
                    }
                }
            }
        }, 100);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'QgCKEditor' => function ($sm) {
                    $config = $sm->getServiceLocator()->get('config');
                    $QuCk = new View\Helper\AdCKEditor($config['adfabcore']['ckeditor']);

                    return $QuCk;
                },

                // This admin navigation layer gives the authentication layer based on BjyAuthorize ;)
                'adminMenu' => function($sm){
                    $nav = $sm->get('navigation')->menu('admin_navigation');
                    $serviceLocator = $sm->getServiceLocator();
                    $nav->setUlClass('nav')
                        ->setMaxDepth(10)
                        ->setRenderInvisible(false);

                    return $nav;
                },

                'googleAnalytics' => function($sm) {
                $tracker = $sm->getServiceLocator()->get('google-analytics');

                $helper  = new View\Helper\GoogleAnalytics($tracker, $sm->getServiceLocator()->get('Request'));

                return $helper;
                },
                
                'adminAssetPath' => function($sm) {
                	$config = $sm->getServiceLocator()->has('Config') ? $sm->getServiceLocator()->get('Config') : array();
                	$helper  = new View\Helper\AdminAssetPath;
                	if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                		$basePath = $config['view_manager']['base_path'];
                	} else {
                		$basePath = $sm->getServiceLocator()->get('Request')->getBasePath();
                	}
                	$helper->setBasePath($basePath);
                	return $helper;
                },
                
                'frontendAssetPath' => function($sm) {
                	$config = $sm->getServiceLocator()->has('Config') ? $sm->getServiceLocator()->get('Config') : array();
                	$helper  = new View\Helper\FrontendAssetPath;
                	if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
                		$basePath = $config['view_manager']['base_path'];
                	} else {
                		$basePath = $sm->getServiceLocator()->get('Request')->getBasePath();
                	}
                	$helper->setBasePath($basePath);
                	return $helper;
                }
            ),
        );

    }

    public function getServiceConfig()
    {
        return array(

                'aliases' => array(
                    'adfabcore_doctrine_em' => 'doctrine.entitymanager.orm_default',
                    'google-analytics'      => 'AdfabCore\Analytics\Tracker',
                ),

                'shared' => array(
                    'adfabcore_message' => false
                ),

                'invokables' => array(
                    'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
                    'adfabcore_message'       => 'AdfabCore\Mail\Service\Message',
                    'adfabcore_cron_service'  => 'AdfabCore\Service\Cron',
                    'adfabcore_shortenurl_service'  => 'AdfabCore\Service\ShortenUrl',

                ),
                'factories' => array(
                    'adfabcore_module_options' => function ($sm) {
                        $config = $sm->get('Configuration');

                        return new Options\ModuleOptions(isset($config['adfabcore']) ? $config['adfabcore'] : array());
                    },
                    'adfabcore_transport' => 'AdfabCore\Mail\Transport\Service\TransportFactory',
                    'admin_navigation' => 'AdfabCore\Service\AdminNavigationFactory',
                    'AdfabCore\Analytics\Tracker' => function($sm) {
                        $config = $sm->get('config');
                        $config = isset($config['adfabcore']) ? $config['adfabcore']['googleAnalytics'] : array('id' => 'UA-XXXXXXXX-X');

                        $tracker = new Analytics\Tracker($config['id']);

						if (isset($config['custom_vars'])) {
							foreach($config['custom_vars'] as $customVar) {
								$customVarId 		= $customVar['id'];
								$customVarName 		= $customVar['name'];
								$customVarValue 	= $customVar['value'];
								$customVarOptScope  = $customVar['optScope'];
								$customVar = new Analytics\CustomVar ($customVarId, $customVarName, $customVarValue, $customVarOptScope);
								$tracker->addCustomVar($customVar);
							}
						}

                        if (isset($config['domain_name'])) {
                            $tracker->setDomainName($config['domain_name']);
                        }

                        if (isset($config['allow_linker'])) {
                            $tracker->setAllowLinker($config['allow_linker']);
                        }

						if (isset($config['allow_hash'])) {
                            $tracker->setAllowHash($config['allow_hash']);
                        }

                        return $tracker;
                    },
                ),
        );
    }
}

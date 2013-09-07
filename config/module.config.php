<?php
return array(
	'service_manager' => array(
		'factories' => array(
			// this definition has to be done here to override Wilmogrod Assetic declaration
			'AsseticBundle\Service' => 'AdfabCore\Assetic\ServiceFactory',
			'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
			'nav' => 'Zend\Navigation\Service\DefaultNavigationFactory',
		),
	),

	'assetic_configuration' => array(
		'buildOnRequest' => true,
		'debug' => false,
		'acceptableErrors' => array(
			//defaults
			\Zend\Mvc\Application::ERROR_CONTROLLER_NOT_FOUND,
			\Zend\Mvc\Application::ERROR_CONTROLLER_INVALID,
			\Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,
			//allow assets when authorisation fails when using the BjyAuthorize module
			\BjyAuthorize\Guard\Route::ERROR,
		),

		'webPath' => __DIR__ . '/../../../../public',
		'cacheEnabled' => false,
		'cachePath' => __DIR__ . '/../../../../data/cache',
		'modules' => array(
			'admin' => array(
				# module root path for your css and js files
				'root_path' => array(
						__DIR__ . '/../view/admin/assets',
				),
				# collection of assets
				'collections' => array(
					'admin_css' => array(
						'assets' => array(
							'bootstrap.min.css' 			 => 'css/bootstrap.min.css',
							'bootstrap-responsive.min.css'   => 'css/bootstrap-responsive.min.css',
							'ie8.css' 						 => 'css/ie8.css',
							'ie.css' 						 => 'css/ie.css',
							'administration.css' 			 => 'css/administration.css',
							'jquery-ui.css' 				 => 'http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css',
							'datepicker.css' 				 => 'css/lib/datepicker.css',
							'jquery-ui-timepicker-addon.css' => 'css/lib/jquery-ui-timepicker-addon.css',
						),
						'filters' => array(),
						'options' => array(
							'output' => 'zfcadmin/css/main'
						),
					),
					'head_admin_js' => array(
						'assets' => array(
							'jquery-1.9.0.min.js' 			=> 'js/lib/jquery-1.9.0.min.js',
							'admin.js'						=> 'js/admin/admin.js',
							'jquery-validate.js'			=> 'js/lib/jquery.validate.min.js',
							'jquery-ui.min.js' 				=> 'js/lib/jquery-ui.min.js',
							'bootstrap.min.js'				=> 'js/lib/bootstrap.min.js',
							'jquery-ui-timepicker-addon.js' => 'js/lib/jquery-ui-timepicker-addon.js',
							'json.js'						=> 'js/lib/json.js',
							'drag.js'						=> 'js/admin/drag.js',
						),
						'filters' => array(),
						'options' => array(
							'output' => 'zfcadmin/js/head_main',
						),
					),
					'admin_images' => array(
						'assets' => array(
							'images/**/*.jpg',
							'images/**/*.png',

						),
						'options' => array(
							'move_raw' => true,
							'output' => 'zfcadmin',
						)
					),
					'admin_fonts' => array(
		 				'assets' => array(
							'fonts/**/*.eot',
							'fonts/**/*.svg',
							'fonts/**/*.ttf',
							'fonts/**/*.woff',
						),
						'options' => array(
							'move_raw' => true,
							'output' => 'zfcadmin',
						)
					),
					'admin_ckeditor' => array(
						'assets' => array(
							'js/lib/ckeditor/*',
							'js/lib/ckeditor/**/*',
							'js/lib/ckeditor/**/**/*',
							'js/lib/ckeditor/**/**/**/*',
							'js/ckeditor-custom/*',
							'js/ckeditor-custom/**/*',
							'css/ckeditor-custom/*',
						),
						'options' => array(
							'move_raw' => true,
							'output' => 'zfcadmin',
						)
					),
					'admin_formgen' => array(
						'assets' => array(
							'js/form/*',
						),
						'options' => array(
							'move_raw' => true,
							'output' => 'zfcadmin',
						)
					),
				),
			),
			'frontend' => array(
				# module root path for your css and js files
				'root_path' => array(
					__DIR__ . '/../view/frontend/assets',
				),
				# collection of assets
				'collections' => array(
					'frontend_css' => array(
						'assets' => array(
							'ie7.css' 				 => 'css/ie7.css',
							'ie8.css' 				 => 'css/ie8.css',
							'ie.css' 				 => 'css/ie.css',
							'styles.css' 			 => 'css/styles.css',
							'uniform.default.css' 	 => 'css/uniform.default.css',
						),
						'filters' => array(),
						'options' => array(
							'output' => 'frontend/css/main'
						),
					),
					'head_frontend_js' => array(
						'assets' => array(
							//'html5.js' => 'js/html5.js',
							//'pie.js' => 'js/lib/pie.js',
							//'selectivizr-min.js' => 'js/lib/selectivizr-min.js',
							'jquery-1.9.0.min.js' => 'js/lib/jquery-1.9.0.min.js',
							'jquery-ui.js' => 'http://code.jquery.com/ui/1.10.2/jquery-ui.js',
							'bowser.min.js' => 'js/lib/bowser.min.js',
							'loader.js' => 'js/loader.js',
							'popin.js' => 'js/popin.js',
							'jscrollpane.js' => 'js/lib/jscrollpane.js',
							'mousewheel.js' => 'js/lib/mousewheel.js',
							'jquery.validate.min.js'=> 'js/lib/jquery.validate.min.js',
							'jquery.nivo.slider.js' => 'js/lib/jquery.nivo.slider.js',
							'jquery.uniform-2.0.js' => 'js/lib/jquery.uniform-2.0.js',
							'jquery.limit-1.2.source.js' => 'js/lib/jquery.limit-1.2.source.js',
							'wScratchpad.js' => 'js/lib/wScratchPad.js',
							'jquery.timer.js' => 'js/lib/jquery.timer.js',
							'sniffer.js' => 'js/sniffer.js',
							'functions.js' => 'js/functions.js',
							'script.js' => 'js/script.js',
							'users.js' => 'js/users.js',
							'share.js' => 'js/share.js',
							'games.js' => 'js/games.js',
							'bootstrap.min.js' => 'js/bootstrap.min.js',
						),
						'filters' => array(),
						'options' => array(
							'output' => 'frontend/js/head_main',
						),
					),
					'frontend_images' => array(
						'assets' => array(
							'images/**/*.png',
							'images/**/*.jpg',
						),
						'options' => array(
							'move_raw' => true,
							'output' => 'frontend',
						)
					),
					'frontend_fonts' => array(
						'assets' => array(
							'fonts/**/*.eot',
							'fonts/**/*.svg',
							'fonts/**/*.ttf',
							'fonts/**/*.woff',
						),
						'options' => array(
							'move_raw' => true,
							'output' => 'frontend'
						)
					),
				),
			),
		),

		'routes' => array(
			'admin.*' => array(
                '@admin_css',
				'@head_admin_js',
            ),
			'frontend.*' => array(
				'@frontend_css',
				'@head_frontend_js',
			),
		),
	),

    'doctrine' => array(
        'driver' => array(
            'adfabcore_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/AdfabCore/Entity'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'AdfabCore\Entity'  => 'adfabcore_entity'
                )
            )
        )
    ),

    'session' => array(
        'remember_me_seconds' => 2419200,
        'use_cookies' => true,
        'cookie_httponly' => true,
    ),
		
    'router' => array(
        'routes' => array(
            'frontend' => array(
            	'type' => 'AdfabCore\Mvc\Router\Http\RegexSlash',
            	'options' => array(
            		'regex'    => '/(?<channel>[embed|facebook|platform|mobile]+)?(/)?',
            		'defaults' => array(
         				'controller' => 'AdfabCore\Controller\Dashboard',
            			'action'     => 'index',
            		),
            		'spec' => '/%channel%/',
            	),
           		'may_terminate' => true,
        		'child_routes' => array(
		            'elfinder' => array(
		                'type' => 'Literal',
		                'options' => array(
		                    'route' => 'elfinder',
		                    'defaults' => array(
		                        'controller' => 'elfinder',
		                        'action'     => 'index',
		                    ),
		                ),
		                'may_terminate' => true,
		                'child_routes' => array(
		                    'connector' => array(
		                        'type' => 'Literal',
		                        'options' => array(
		                            'route' => '/connector',
		                            'defaults' => array(
		                                'controller' => 'elfinder',
		                                'action'     => 'connector',
		                            ),
		                        ),
		                    ),
		                    'ckeditor' => array(
		                        'type' => 'Literal',
		                        'options' => array(
		                            'route' => '/ckeditor',
		                            'defaults' => array(
		                                'controller' => 'elfinder',
		                                'action'     => 'ckeditor',
		                            ),
		                        ),
		                    ),
		                ),
		            ),
		            // Give the possibility to call Cron from browser
		            'cron' => array(
		                'type' => 'Literal',
		                'options' => array(
		                    'route' => 'cron',
		                    'defaults' => array(
		                        'controller' => 'adfabcore_console',
		                        'action' => 'cron'
		                    ),
		                ),
		            ),
        		),
        	),
            'admin' => array(
                'type' => 'Literal',
            	'priority' => 1000,
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'AdfabCore\Controller\Dashboard',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                	'dashboard' => array(
               			'type' => 'literal',
              			'options' => array(
              				'route'    => '/dashboard',
                			'defaults' => array(
               					'controller' => 'AdfabCore\Controller\Dashboard',
                				'action'     => 'index',
                			),
               			),
                	),
                    'system' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route'    => '/system',
                            'defaults' => array(
                                'controller' => 'AdfabCore\Controller\System',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'modules' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route'    => '/modules',
                                    'defaults' => array(
                                            'controller' => 'AdfabCore\Controller\System',
                                            'action'     => 'modules',
                                    ),
                                )
                            ),
                            'settings' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route'    => '/settings',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\System',
                                        'action'     => 'settings',
                                    ),
                                )
                            ),
                        )
                    ),
                    'formgen' => array(
                        'type'    => 'Literal',
                        'options' => array(
                                'route'    => '/formgen',
                                'defaults' => array(
                                        'controller'    => 'AdfabCore\Controller\Formgen',
                                        'action'        => 'index',
                                ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'create' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/create',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'create',
                                    ),
                                ),
                            ),
                            'view' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/view[/:form]',
                                    'constraints' => array(
                                        'form' => '[a-zA-Z0-9_-]+'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'view',
                                    ),
                                ),
                            ),
                            'input' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/input',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'input',
                                    ),
                                ),
                            ),
                            'paragraph' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/paragraph',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'paragraph',
                                    ),
                                ),
                            ),
                            'number' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/number',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'number',
                                    ),
                                ),
                            ),
                            'phone' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/phone',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'phone',
                                    ),
                                ),
                            ),
                            'checkbox' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/checkbox',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'checkbox',
                                    ),
                                ),
                            ),
                            'radio' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/radio',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'radio',
                                    ),
                                ),
                            ),
                            'dropdown' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/dropdown',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'dropdown',
                                    ),
                                ),
                            ),
                            'password' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/password',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'password',
                                    ),
                                ),
                            ),
                            'passwordverify' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/passwordverify',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'passwordverify',
                                    ),
                                ),
                            ),
                            'email' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/email',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'email',
                                    ),
                                ),
                            ),
                            'date' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/date',
                                    'defaults' => array(
                                        'controller' => 'Index',
                                        'action'     => 'date',
                                    ),
                                ),
                            ),
                            'upload' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/upload',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'upload',
                                    ),
                                ),
                            ),
                            'creditcard' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/creditcard',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'creditcard',
                                    ),
                                ),
                            ),
                            'url' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/url',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'url',
                                    ),
                                ),
                            ),
                            'hidden' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/hidden',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'hidden',
                                    ),
                                ),
                            ),
                            'test' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/test',
                                    'defaults' => array(
                                        'controller' => 'AdfabCore\Controller\Formgen',
                                        'action'     => 'test',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'cron' => array(
                    'options' => array(
                        'route' => 'cron',
                        'defaults' => array(
                            'controller' => 'adfabcore_console',
                            'action' => 'cron'
                        ),
                    ),
                ),
            )
        )
    ),

	'core_layout' => array(
        'AdfabCore' => array(
            'default_layout' => 'layout/homepage-2columns-right',
            'controllers' => array(
            	'AdfabCore\Controller\Dashboard'   => array(
            		'default_layout' => 'layout/admin',
            	),	
            ),
        ),
	),
		
		
    'controllers' => array(
        'invokables' => array(
            'AdfabCore\Controller\Dashboard' => 'AdfabCore\Controller\DashboardController',
            'AdfabCore\Controller\System'    => 'AdfabCore\Controller\SystemController',
            'AdfabCore\Controller\Formgen'   => 'AdfabCore\Controller\FormgenController',
            'adfabcore_console'              => 'AdfabCore\Controller\ConsoleController',
            'elfinder'                       => 'AdfabCore\Controller\ElfinderController',
            'facebook'                       => 'AdfabCore\Controller\FacebookController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'shortenUrl' => 'AdfabCore\Controller\Plugin\ShortenUrl',
        ),
    ),
    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type'         => 'phpArray',
                'base_dir'     => __DIR__ . '/../language',
                'pattern'      => '%s.php',
                'text_domain'  => 'adfabcore'
            ),
        ),
    ),

    /*'navigation' => array(
        'admin' => array(
            'system' => array(
                'label' => 'System',
                'id' => 'system-page',
                'route' => 'admin/system',
                'order' => 100,
                'resource' => 'core',
                'privilege' => 'edit',
                'pages' => array(
                    'settings' => array(
                        'label' => 'Settings',
                        'route' => 'admin/system/settings',
                        'resource' => 'core',
                        'privilege' => 'edit',
                    ),
                ),
            ),
        ),
    ),*/

    'navigation' => array(
        'admin' => array(
            'home' => array(
                'label' => 'Accueil',
                'route' => 'admin',
                'order' => -100,
                'resource' => 'core',
                'privilege' => 'edit',
            ),
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'XHTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_path_stack' => array(
            __DIR__ . '/../view/admin',
            __DIR__ . '/../view/frontend',
        ),
    ),
		
	/*'design' => array(
		'admin' => array(
			'package' => 'default',
			'theme' => 'base',
		),
		'frontend' => array(
			'package' => 'default',
			'theme' => 'base',
		),
	),*/
);

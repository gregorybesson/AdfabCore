<?php
return array(
	'assetic_configuration' => array(
		'modules' => array(
			'core' => array(
				# module root path for your css and js files
				'root_path' => __DIR__ . '/../assets',
				# collection of assets
				'collections' => array(
					'admin_css' => array(
						'assets' => array(
							'css/bootstrap.min.css',
							'css/bootstrap-responsive.min.css',
							'css/ie8.css',
							'css/ie.css',
							'css/administration.css',
							'http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css',
							'css/lib/datepicker.css',
							'css/lib/jquery-ui-timepicker-addon.css',
						),
						'filters' => array(
							'CssRewriteFilter' => array(
								'name' => 'Assetic\Filter\CssRewriteFilter',
							)
						),
						'options' => array(),
					),
					'admin_js' => array(
						'assets' => array(
							'js/lib/jquery-1.9.0.min.js',
							'js/lib/html5.js',
							'js/lib/bootstrap.min.js',
							'js/lib/jquery-ui-timepicker-addon.js',
							'js/lib/bootstrap-datepicker.js',
							'js/lib/json.js',
							'http://code.jquery.com/ui/1.10.2/jquery-ui.js',
							'js/admin/admin.js',
							'js/admin/drag.js',
						),
						'filters' => array(),
						'options' => array(),
					),
				),
			),
		),
			
		'routes' => array(
			'zfcadmin.*' => array(
                '@admin_css', 
				'@admin_js',
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
            'elfinder' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/elfinder',
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
                    'route' => '/cron',
                    'defaults' => array(
                        'controller' => 'adfabcore_console',
                        'action' => 'cron'
                    ),
                ),
            ),
            'zfcadmin' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'AdfabCore\Controller\Dashboard',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
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
                'route' => 'zfcadmin/system',
                'order' => 100,
                'resource' => 'core',
                'privilege' => 'edit',
                'pages' => array(
                    'settings' => array(
                        'label' => 'Settings',
                        'route' => 'zfcadmin/system/settings',
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
                'route' => 'zfcadmin',
                'order' => -100,
                'resource' => 'core',
                'privilege' => 'edit',
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);

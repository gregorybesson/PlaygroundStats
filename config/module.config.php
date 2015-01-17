<?php

return array(

    'bjyauthorize' => array(
    
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'stats'         => array(),
            ),
        ),
    
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('admin'), 'stats',          array('list')),
                ),
            ),
        ),
    
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'adminstats',                                             'roles' => array('admin')),
            ),
        ),
    ),
    
	'assetic_configuration' => array(
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
							'jquery-gridster.css'   => 'css/jquery.gridster.min.css',
							'gridster.css' 			=> 'css/gridster.css',
						),
					),
					'head_admin_js' => array(
						'assets' => array(
							'jquery-gridster.js' 			 => 'js/jquery.gridster.min.js',
						),
					),
					'gridster_images' => array(
						'assets' => array(
							'images/**/*.jpg',
							'images/**/*.png',

						),
						'options' => array(
							'move_raw' => true,
							'output' => 'zfcadmin',
						)
					),
				),
			),
			'stats' => array(
				# module root path for your css and js files
				'root_path' => array(
					__DIR__ . '/../view/admin/assets',
				),
				# collection of assets
				'collections' => array(
					'gridster_images' => array(
						'assets' => array(
							'images/**/*.jpg',
							'images/**/*.png',
						),
						'options' => array(
							'move_raw' => true,
							'output' => 'zfcadmin',
						)
					),
				),
			),
		),
	),

    'router' => array(
        'routes' => array(
             'admin' => array(
                'child_routes' => array(
                    'stats' => array(
                	    'type' => 'Literal',
                    	'options' => array(
                    		'route' => '/stats',
                            'defaults' => array(
                                'controller' => 'adminstats',
                                'action' => 'index',
                            ),
                        ),
                    	'may_terminate' => true,
                        'child_routes' =>array(
                            'statistics' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/summary',
                                    'defaults' => array(
                                        'controller' => 'adminstats',
                                        'action' => 'statistics',
                                    ),
                                ),
                            ),
		                    'share' => array(
		                        'type' => 'Literal',
		                        'options' => array(
		                            'route' => '/share',
		                            'defaults' => array(
		                                'controller' => 'adminstats',
		                                'action' => 'share',
		                            ),
		                        ),
		                    ),
		                    'badge' => array(
		                        'type' => 'Literal',
		                        'options' => array(
		                            'route' => '/badge',
		                            'defaults' => array(
		                                'controller' => 'adminstats',
		                                'action' => 'badge',
		                            ),
		                        ),
		                    ),
		                    'download' => array(
		                        'type' => 'Literal',
		                        'options' => array(
		                            'route' => '/download',
		                            'defaults' => array(
		                                'controller' => 'adminstats',
		                                'action'     => 'download',
		                            ),
		                        ),
		                    ),
		                    'games' => array(
								'type' => 'literal',
								'options' => array(
									'route' => '/games',
									'defaults' => array(
		                                'controller' => 'adminstats',
		                                'action' => 'games',
		                            ),
								),
							),
							'export' => array(
								'type' => 'literal',
								'options' => array(
									'route' => '/export',
									'defaults' => array(
		                                'controller' => 'adminstats',
		                                'action' => 'export',
		                            ),
								),
							),
							'downloadexport' => array(
		                        'type' => 'Literal',
		                        'options' => array(
		                            'route' => '/download-export',
		                            'defaults' => array(
		                                'controller' => 'adminstats',
		                                'action'     => 'downloadexport',
		                            ),
		                        ),
		                    ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'core_layout' => array(
        'frontend' => array(
            'modules' => array(
                'PlaygroundStats' => array(
                    'default_layout' => 'layout/admin',
                    'controllers' => array(
                    	'adminstats'   => array(
                    		'default_layout' => 'layout/admin',
                    	),
                    ),
                ),
            ),
        ),
    ),

    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type'     => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            	'text_domain'  => 'playgroundstats'
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'adminstats' => 'PlaygroundStats\Controller\Admin\StatisticsController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view/admin',
            __DIR__ . '/../view/frontend',
        ),
    ),

	'navigation' => array(
		'admin' => array(
			'statisticsgames' => array(
				'order' => 1,
				'label' => 'Statistics',
				'route' => 'admin/stats/games',
				'resource' => 'stats',
				'privilege' => 'list',
				'pages' => array(
					'list' => array(
						'label' => 'Game stats',
						'route' => 'admin/stats/games',
						'resource' => 'stats',
						'privilege' => 'list',
					),
					'export' => array(
						'label' => 'Export',
						'route' => 'admin/stats/export',
						'resource' => 'stats',
						'privilege' => 'list',
					),
				),
			),
		),
	),
);

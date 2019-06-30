<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'playgroundstats_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/Entity'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'PlaygroundStats\Entity'  => 'playgroundstats_entity'
                )
            )
        )
    ),

    'bjyauthorize' => array(
    
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'stats' => array(),
            ),
        ),
    
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('admin'), 'stats', array('list')),
                ),
            ),
        ),
    
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class, 'roles' => array('admin')),
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
                                'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
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
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action' => 'statistics',
                                    ),
                                ),
                            ),
                            'update-dashboard' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/update-dashboard',
                                    'defaults' => array(
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action' => 'updateDashboard',
                                    ),
                                ),
                            ),
                            'share' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/share',
                                    'defaults' => array(
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action' => 'share',
                                    ),
                                ),
                            ),
                            'badge' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/badge',
                                    'defaults' => array(
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action' => 'badge',
                                    ),
                                ),
                            ),
                            'download' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/download',
                                    'defaults' => array(
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action'     => 'download',
                                    ),
                                ),
                            ),
                            'games' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/games',
                                    'defaults' => array(
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action' => 'games',
                                    ),
                                ),
                            ),
                            'export' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/export',
                                    'defaults' => array(
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action' => 'export',
                                    ),
                                ),
                            ),
                            'downloadexport' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/download-export',
                                    'defaults' => array(
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action'     => 'downloadexport',
                                    ),
                                ),
                            ),
                            'card' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/card',
                                    'defaults' => array(
                                        'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                        'action'     => 'index',
                                    ),
                                ),
                                'child_routes' =>array(
                                    'list' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/list[/:p]',
                                            'defaults' => array(
                                                'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                                'action'     => 'listCard',
                                            ),
                                        ),
                                    ),
                                    'create' => array(
                                        'type' => 'Zend\Router\Http\Literal',
                                        'options' => array(
                                            'route' => '/create',
                                            'defaults' => array(
                                                'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                                'action'     => 'createCard'
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit/:cardId',
                                            'defaults' => array(
                                                'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                                'action'     => 'editCard',
                                                'cardId'     => 0
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete/:cardId',
                                            'defaults' => array(
                                                'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                                'action'     => 'removeCard',
                                                'cardId'     => 0
                                            ),
                                        ),
                                    ),
                                    'view' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/view/:cardId',
                                            'defaults' => array(
                                                'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
                                                'action'     => 'viewCard',
                                                'cardId'     => 0
                                            ),
                                        ),
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
                        'playgroundstats'   => array(
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
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../../language',
                'pattern' => '%s.php',
                'text_domain' => 'playgroundstats'
            ),
            array(
                'type'     => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
                'text_domain'  => 'playgroundstats'
            ),
        ),
    ),

    'controllers' => array(
        'factories' => array(
            \PlaygroundStats\Controller\Admin\StatisticsController::class => \PlaygroundStats\Controller\Admin\StatisticsControllerFactory::class,
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            \PlaygroundStats\Service\Stats::class => PlaygroundStats\Service\StatsFactory::class,
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
                'target' => 'nav-icon icon-chart',
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

// PlaygroundStats defines itself as the admin Dashboard controller
    'playgrounduser' => array(
        'admin' => array(
            'controller' => \PlaygroundStats\Controller\Admin\StatisticsController::class,
            'action' => 'index'
        ),
    )
);
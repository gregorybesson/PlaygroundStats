<?php

namespace PlaygroundStats;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
    
            'invokables' => array(
                'playgroundstats_stats_service' => 'PlaygroundStats\Service\Stats',
            ),
    
            'factories' => array(
                'playgroundstats_export_form' => function ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Export(null, $sm, $translator);
    
                    return $form;
                },
                'playgroundstats_dashboard_mapper' => function ($sm) {
                    return new Mapper\Dashboard($sm->get('doctrine.entitymanager.orm_default'));
                },
            ),
        );
    }
}

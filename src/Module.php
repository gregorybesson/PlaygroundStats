<?php

namespace PlaygroundStats;

use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Validator\AbstractValidator;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(    
            'factories' => array(
                'playgroundstats_export_form' => function ($sm) {
                    $translator = $sm->get('MvcTranslator');
                    $form = new Form\Export(null, $sm, $translator);
    
                    return $form;
                },
                'playgroundstats_card_form' => function ($sm) {
                    $translator = $sm->get('MvcTranslator');
                    $form = new Form\Card(null, $sm, $translator);
    
                    return $form;
                },
                'playgroundstats_dashboard_mapper' => function (\Laminas\ServiceManager\ServiceManager $sm) {
                    return new Mapper\Dashboard(
                        $sm->get('doctrine.entitymanager.orm_default'),
                        $sm
                    );
                },
                'playgroundstats_card_mapper' => function (\Laminas\ServiceManager\ServiceManager $sm) {
                    return new Mapper\Card(
                        $sm->get('doctrine.entitymanager.orm_default'),
                        $sm
                    );
                },
            ),
        );
    }
}

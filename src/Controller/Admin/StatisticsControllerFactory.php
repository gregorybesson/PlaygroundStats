<?php
namespace PlaygroundStats\Controller\Admin;

use PlaygroundStats\Controller\Admin\StatisticsController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class StatisticsControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new StatisticsController($container);

        return $controller;
    }
}

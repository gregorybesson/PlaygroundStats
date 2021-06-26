<?php
namespace PlaygroundStats\Service;

use PlaygroundStats\Service\Stats;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class StatsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Stats($container);

        return $service;
    }
}

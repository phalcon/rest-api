<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Application;

class ModulesProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Config $config */
        $config = $container->getShared('config');

        /** @var Application $application */
        $application = $container->getShared('application');
        $modules     = $config->path('modules')->toArray();

        $application->registerModules($modules);
    }
}

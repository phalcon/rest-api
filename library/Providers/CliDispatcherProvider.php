<?php

declare(strict_types=1);

namespace Gewaer\Providers;

use Phalcon\Cli\Dispatcher;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class CliDispatcherProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $config = $container->getShared('config');

        $container->setShared(
            'dispatcher',
            function () use ($config) {
                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace(ucfirst($config->app->namespaceName) . '\Cli\Tasks');

                return $dispatcher;
            }
        );
    }
}

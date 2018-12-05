<?php

namespace Gewaer\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Gewaer\Models\Apps;

class AppProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $config = $container->getShared('config');

        $container->setShared(
            'app',
            function () use ($config) {
                return Apps::findFirst($config->app->id);
            }
        );
    }
}

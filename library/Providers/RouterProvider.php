<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Collection;

class RouterProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Config $config */
        $config        = $container->getShared('config');
        /** @var Micro $application */
        $application   = $container->getShared('application');
        /** @var Manager $eventsManager */
        $eventsManager = $container->getShared('eventsManager');
        $routes        = $config->path('routes')->toArray();
        $middleware    = $config->path('middleware')->toArray();

        foreach ($routes as $route) {
            $collection = new Collection();
            $collection->setHandler($route['class'], true);
            if (true !== empty($route['prefix'])) {
                $collection->setPrefix($route['prefix']);
            }

            foreach ($route['methods'] as $verb => $methods) {
                foreach ($methods as $endpoint => $action) {
                    $collection->$verb($endpoint, $action);
                }
            }

            $application->mount($collection);
        }

        /**
         * Get the events manager and attach the middleware to it
         */
        foreach ($middleware as $class) {
            $eventsManager->attach('micro', new $class());
            $application->before(new $class());
        }

        $application->setEventsManager($eventsManager);
    }
}

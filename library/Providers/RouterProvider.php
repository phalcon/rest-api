<?php

namespace Niden\Providers;

use Niden\Api\Controllers\IndexController;
use Niden\Middleware\NotFoundMiddleware;
use Niden\Middleware\PayloadMiddleware;
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

        $this->attachRoutes($application);
        $this->attachMiddleware($application, $eventsManager);

        $application->setEventsManager($eventsManager);
    }

    /**
     * Attaches the middleware to the application
     *
     * @param Micro   $application
     * @param Manager $eventsManager
     */
    private function attachMiddleware(Micro $application, Manager $eventsManager)
    {
        $middleware = [
            NotFoundMiddleware::class,
            PayloadMiddleware::class,
        ];

        /**
         * Get the events manager and attach the middleware to it
         */
        foreach ($middleware as $class) {
            $eventsManager->attach('micro', new $class());
            $application->before(new $class());
        }
    }

    /**
     * Attaches the routes to the application; lazy loaded
     *
     * @param Micro $application
     */
    private function attachRoutes(Micro $application)
    {

        $routes = [
            [
                'class'    => IndexController::class,
                'prefix'   => '',
                'methods'  => [
                    'get'  => [
                        '/'       => 'indexAction',
                    ],
                ],
            ],
        ];

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
    }
}

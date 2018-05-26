<?php

namespace Niden\Providers;

use Niden\Api\Controllers\IndexController;
use Niden\Api\Controllers\UserGetController;
use Niden\Api\Controllers\LoginController;
use Niden\Middleware\NotFoundMiddleware;
use Niden\Middleware\PayloadMiddleware;
use Niden\Middleware\AuthenticationMiddleware;
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
        $middleware = $this->getMiddleware();

        /**
         * Get the events manager and attach the middleware to it
         */
        foreach ($middleware as $class => $function) {
            $eventsManager->attach('micro', new $class());
            $application->{$function}(new $class());
        }
    }

    /**
     * Attaches the routes to the application; lazy loaded
     *
     * @param Micro $application
     */
    private function attachRoutes(Micro $application)
    {
        $routes = $this->getRoutes();

        foreach ($routes as $route) {
            $collection = new Collection();
            $collection
                ->setHandler($route[0], true)
                ->setPrefix($route[1])
                ->{$route[2]}($route[3], $route[4]);

            $application->mount($collection);
        }
    }

    /**
     * Returns the array for the middleware with the action to attach
     *
     * @return array
     */
    private function getMiddleware(): array
    {
        return [
            NotFoundMiddleware::class       => 'before',
            PayloadMiddleware::class        => 'before',
            AuthenticationMiddleware::class => 'before',
        ];
    }

    /**
     * Returns the array for the routes
     *
     * @return array
     */
    private function getRoutes(): array
    {
        return [
            // Class, Method, Route, Handler
            [IndexController::class, '', 'get', '/', 'indexAction'],
            [IndexController::class, '', 'post', '/', 'indexAction'],
            [LoginController::class, '', 'post', '/login', 'indexAction'],
            [UserGetController::class, '/user', 'post', '/get', 'getAction'],
        ];
    }
}

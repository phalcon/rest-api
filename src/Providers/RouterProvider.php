<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Providers;

use Phalcon\Api\Api\Controllers\Companies\AddController as CompaniesAddController;
use Phalcon\Api\Api\Controllers\Companies\GetController as CompaniesGetController;
use Phalcon\Api\Api\Controllers\Individuals\GetController as IndividualsGetController;
use Phalcon\Api\Api\Controllers\IndividualTypes\GetController as IndividualTypesGetController;
use Phalcon\Api\Api\Controllers\LoginController;
use Phalcon\Api\Api\Controllers\Products\GetController as ProductsGetController;
use Phalcon\Api\Api\Controllers\ProductTypes\GetController as ProductTypesGetController;
use Phalcon\Api\Api\Controllers\Users\GetController as UsersGetController;
use Phalcon\Api\Constants\Relationships as Rel;
use Phalcon\Api\Middleware\AuthenticationMiddleware;
use Phalcon\Api\Middleware\NotFoundMiddleware;
use Phalcon\Api\Middleware\ResponseMiddleware;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
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
    public function register(DiInterface $container): void
    {
        /** @var Micro $application */
        $application = $container->getShared('application');
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
     *
     * @return void
     */
    private function attachMiddleware(
        Micro $application,
        Manager $eventsManager
    ): void {
        $middleware = $this->getMiddleware();

        /**
         * Get the events manager and attach the middleware to it. One instance
         * per middleware, shared by the events manager and the application -
         * two instances would give each its own state.
         */
        foreach ($middleware as $class => $function) {
            $object = new $class();

            $eventsManager->attach('micro', $object);
            $application->{$function}($object);
        }
    }

    /**
     * Attaches the routes to the application; lazy loaded
     *
     * @param Micro $application
     *
     * @return void
     */
    private function attachRoutes(Micro $application): void
    {
        $routes = $this->getRoutes();

        foreach ($routes as $route) {
            $collection = new Collection();
            $collection
                ->setHandler($route[0], true)
                ->setPrefix($route[1])
                ->{$route[2]}($route[3], 'callAction')
            ;

            $application->mount($collection);
        }
    }

    /**
     * Returns the array for the middleware with the action to attach
     *
     * @return array<class-string, string> Middleware class => the Micro hook it attaches to
     */
    private function getMiddleware(): array
    {
        return [
            NotFoundMiddleware::class       => 'before',
            AuthenticationMiddleware::class => 'before',
            ResponseMiddleware::class       => 'after',
        ];
    }

    /**
     * Adds multiple routes for the same handler abiding by the JSONAPI standard
     *
     * @param array<int, array{0: class-string, 1: string, 2: string, 3: string}> $routes
     * @param class-string                                                        $class
     * @param string                                                              $relationship
     *
     * @return array<int, array{0: class-string, 1: string, 2: string, 3: string}>
     */
    private function getMultiRoutes(
        array $routes,
        string $class,
        string $relationship
    ): array {
        $routes[] = [$class, '/' . $relationship, 'get', '/'];
        $routes[] = [$class, '/' . $relationship, 'get', '/{recordId:[0-9]+}'];
        $routes[] = [$class, '/' . $relationship, 'get', '/{recordId:[0-9]+}/{relationships:[a-zA-Z-,.]+}'];
        $routes[] = [
            $class,
            '/' . $relationship,
            'get',
            '/{recordId:[0-9]+}/relationships/{relationships:[a-zA-Z-,.]+}',
        ];

        return $routes;
    }

    /**
     * Returns the array for the routes
     *
     * @return array<int, array{0: class-string, 1: string, 2: string, 3: string}> Handler, prefix, verb, route
     */
    private function getRoutes(): array
    {
        $routes = [
            // Class, Method, Route, Handler
            [LoginController::class, '/login', 'post', '/'],
            [CompaniesAddController::class, '/companies', 'post', '/'],
            [UsersGetController::class, '/users', 'get', '/'],
            [UsersGetController::class, '/users', 'get', '/{recordId:[0-9]+}'],
        ];

        $routes = $this->getMultiRoutes(
            $routes,
            CompaniesGetController::class,
            Rel::COMPANIES
        );
        $routes = $this->getMultiRoutes(
            $routes,
            IndividualsGetController::class,
            Rel::INDIVIDUALS
        );
        $routes = $this->getMultiRoutes(
            $routes,
            IndividualTypesGetController::class,
            Rel::INDIVIDUAL_TYPES
        );
        $routes = $this->getMultiRoutes(
            $routes,
            ProductsGetController::class,
            Rel::PRODUCTS
        );
        $routes = $this->getMultiRoutes(
            $routes,
            ProductTypesGetController::class,
            Rel::PRODUCT_TYPES
        );

        return $routes;
    }
}

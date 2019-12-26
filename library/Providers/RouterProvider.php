<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
use Phalcon\Api\Middleware\TokenUserMiddleware;
use Phalcon\Api\Middleware\TokenValidationMiddleware;
use Phalcon\Api\Middleware\TokenVerificationMiddleware;
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
                ->{$route[2]}($route[3], 'callAction');

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
            NotFoundMiddleware::class          => 'before',
            AuthenticationMiddleware::class    => 'before',
            TokenUserMiddleware::class         => 'before',
            TokenVerificationMiddleware::class => 'before',
            TokenValidationMiddleware::class   => 'before',
            ResponseMiddleware::class          => 'after',
        ];
    }

    /**
     * Returns the array for the routes
     *
     * @return array
     */
    private function getRoutes(): array
    {
        $routes = [
            // Class, Method, Route, Handler
            [LoginController::class,        '/login',     'post', '/'],
            [CompaniesAddController::class, '/companies', 'post', '/'],
            [UsersGetController::class,     '/users',     'get',  '/'],
            [UsersGetController::class,     '/users',     'get',  '/{recordId:[0-9]+}'],
        ];

        $routes = $this->getMultiRoutes($routes, CompaniesGetController::class, Rel::COMPANIES);
        $routes = $this->getMultiRoutes($routes, IndividualsGetController::class, Rel::INDIVIDUALS);
        $routes = $this->getMultiRoutes($routes, IndividualTypesGetController::class, Rel::INDIVIDUAL_TYPES);
        $routes = $this->getMultiRoutes($routes, ProductsGetController::class, Rel::PRODUCTS);
        $routes = $this->getMultiRoutes($routes, ProductTypesGetController::class, Rel::PRODUCT_TYPES);

        return $routes;
    }

    /**
     * Adds multiple routes for the same handler abiding by the JSONAPI standard
     *
     * @param array  $routes
     * @param string $class
     * @param string $relationship
     *
     * @return array
     */
    private function getMultiRoutes(array $routes, string $class, string $relationship): array
    {
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
}

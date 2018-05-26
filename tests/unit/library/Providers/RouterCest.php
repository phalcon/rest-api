<?php

namespace Niden\Tests\unit\library\Providers;

use Niden\Logger;
use Niden\Providers\ConfigProvider;
use Niden\Providers\LoggerProvider;
use Niden\Providers\RouterProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\RouterInterface;
use \UnitTester;

class RouterCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $application = new Micro($diContainer);
        $diContainer->setShared('application', $application);
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider    = new RouterProvider();
        $provider->register($diContainer);

        /** @var RouterInterface $router */
        $router = $application->getRouter();
        $routes = $router->getRoutes();
        $I->assertEquals(4, count($routes));
        $I->assertEquals('GET', $routes[0]->getHttpMethods());
        $I->assertEquals('/', $routes[0]->getPattern());
        $I->assertEquals('POST', $routes[1]->getHttpMethods());
        $I->assertEquals('/', $routes[1]->getPattern());
        $I->assertEquals('POST', $routes[2]->getHttpMethods());
        $I->assertEquals('/login', $routes[2]->getPattern());
        $I->assertEquals('POST', $routes[3]->getHttpMethods());
        $I->assertEquals('/user/get', $routes[3]->getPattern());
    }
}

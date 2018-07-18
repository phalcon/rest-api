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
        $I->assertEquals(9, count($routes));
        $I->assertEquals('POST', $routes[0]->getHttpMethods());
        $I->assertEquals('/login', $routes[0]->getPattern());
        $I->assertEquals('POST', $routes[1]->getHttpMethods());
        $I->assertEquals('/companies', $routes[1]->getPattern());
        $I->assertEquals('POST', $routes[2]->getHttpMethods());
        $I->assertEquals('/companies/{companyId:[0-9]+}', $routes[2]->getPattern());
        $I->assertEquals('GET', $routes[3]->getHttpMethods());
        $I->assertEquals('/individualtypes', $routes[3]->getPattern());
        $I->assertEquals('GET', $routes[4]->getHttpMethods());
        $I->assertEquals('/individualtypes/{typeId:[0-9]+}', $routes[4]->getPattern());
        $I->assertEquals('GET', $routes[5]->getHttpMethods());
        $I->assertEquals('/producttypes', $routes[5]->getPattern());
        $I->assertEquals('GET', $routes[6]->getHttpMethods());
        $I->assertEquals('/producttypes/{typeId:[0-9]+}', $routes[6]->getPattern());
        $I->assertEquals('GET', $routes[7]->getHttpMethods());
        $I->assertEquals('/users', $routes[7]->getPattern());
        $I->assertEquals('GET', $routes[8]->getHttpMethods());
        $I->assertEquals('/users/{userId:[0-9]+}', $routes[8]->getPattern());
   }
}

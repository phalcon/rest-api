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
        $expected = [
            ['POST', '/login'],
            ['POST', '/companies'],
            ['GET',  '/companies'],
            ['GET',  '/companies/{companyId:[0-9]+}'],
            ['GET',  '/companies/relationships/{relationships:[a-zA-Z,.}'],
            ['GET',  '/individual-types'],
            ['GET',  '/individual-types/{typeId:[0-9]+}'],
            ['GET',  '/products'],
            ['GET',  '/products/{productId:[0-9]+}'],
            ['GET',  '/product-types'],
            ['GET',  '/product-types/{typeId:[0-9]+}'],
            ['GET',  '/users'],
            ['GET',  '/users/{userId:[0-9]+}'],
        ];

        $I->assertEquals(13, count($routes));
        foreach ($routes as $index => $route) {
            $I->assertEquals($expected[$index][0], $route->getHttpMethods());
            $I->assertEquals($expected[$index][1], $route->getPattern());
        }
   }
}

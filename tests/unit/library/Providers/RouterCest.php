<?php

namespace Phalcon\Api\Tests\unit\library\Providers;

use Phalcon\Api\Logger;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\RouterProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\RouterInterface;
use UnitTester;

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
        $provider = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new RouterProvider();
        $provider->register($diContainer);

        /** @var RouterInterface $router */
        $router   = $application->getRouter();
        $routes   = $router->getRoutes();
        $expected = [
            ['POST', '/login'],
            ['POST', '/companies'],
            ['GET', '/users'],
            ['GET', '/users/{recordId:[0-9]+}'],
            ['GET', '/companies'],
            ['GET', '/companies/{recordId:[0-9]+}'],
            ['GET', '/companies/{recordId:[0-9]+}/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/companies/{recordId:[0-9]+}/relationships/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/individuals'],
            ['GET', '/individuals/{recordId:[0-9]+}'],
            ['GET', '/individuals/{recordId:[0-9]+}/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/individuals/{recordId:[0-9]+}/relationships/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/individual-types'],
            ['GET', '/individual-types/{recordId:[0-9]+}'],
            ['GET', '/individual-types/{recordId:[0-9]+}/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/individual-types/{recordId:[0-9]+}/relationships/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/products'],
            ['GET', '/products/{recordId:[0-9]+}'],
            ['GET', '/products/{recordId:[0-9]+}/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/products/{recordId:[0-9]+}/relationships/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/product-types'],
            ['GET', '/product-types/{recordId:[0-9]+}'],
            ['GET', '/product-types/{recordId:[0-9]+}/{relationships:[a-zA-Z-,.]+}'],
            ['GET', '/product-types/{recordId:[0-9]+}/relationships/{relationships:[a-zA-Z-,.]+}'],
        ];

        $I->assertEquals(24, count($routes));
        foreach ($routes as $index => $route) {
            $I->assertEquals($expected[$index][0], $route->getHttpMethods());
            $I->assertEquals($expected[$index][1], $route->getPattern());
        }
    }
}

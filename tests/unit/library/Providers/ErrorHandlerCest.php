<?php

namespace Gewaer\Tests\unit\library\Providers;

use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\ErrorHandlerProvider;
use Gewaer\Providers\LoggerProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;
use function date_default_timezone_get;

class ErrorHandlerCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new LoggerProvider();
        $provider->register($diContainer);
        $provider = new ErrorHandlerProvider();
        $provider->register($diContainer);

        $config = $diContainer->getShared('config');

        $I->assertEquals(date_default_timezone_get(), $config->path('app.timezone'));
        $I->assertEquals(ini_get('display_errors'), 1);
        $I->assertEquals(E_ALL, ini_get('error_reporting'));
    }
}

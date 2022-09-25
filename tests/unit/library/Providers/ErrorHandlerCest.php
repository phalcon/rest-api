<?php

namespace Phalcon\Api\Tests\unit\library\Providers;

use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
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
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new LoggerProvider();
        $provider->register($diContainer);
        $provider = new ErrorHandlerProvider();
        $provider->register($diContainer);

        $config = $diContainer->getShared('config');

        $I->assertSame(date_default_timezone_get(), $config->path('app.timezone'));
        $I->assertSame(ini_get('display_errors'), 'Off');
        $I->assertSame(E_ALL, (int) ini_get('error_reporting'));
    }
}

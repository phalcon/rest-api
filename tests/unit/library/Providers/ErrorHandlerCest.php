<?php

namespace Niden\Tests\unit\library\Providers;

use function date_default_timezone_get;
use const E_NOTICE;
use const E_USER_NOTICE;
use function Niden\Core\appPath;
use Niden\Providers\ConfigProvider;
use Niden\Providers\ErrorHandlerProvider;
use Niden\Providers\LoggerProvider;
use Phalcon\Di\FactoryDefault;
use function trigger_error;
use \UnitTester;

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
        $provider    = new LoggerProvider();
        $provider->register($diContainer);
        $provider    = new ErrorHandlerProvider();
        $provider->register($diContainer);

        $config = $diContainer->getShared('config');

        $I->assertEquals(date_default_timezone_get(), $config->path('app.timezone'));
        $I->assertEquals(ini_get('display_errors'), 'Off');
        $I->assertEquals(E_ALL, ini_get('error_reporting'));
    }
}

<?php

namespace Niden\Tests\unit\config;

use function Niden\Functions\appPath;
use Niden\Providers\ConfigProvider;
use Niden\Providers\ErrorHandlerProvider;
use Niden\Providers\EventsManagerProvider;
use Niden\Providers\LoggerProvider;
use Niden\Providers\ResponseProvider;
use Niden\Providers\RouterProvider;
use \UnitTester;

class ProvidersCest
{
    public function checkProviders(UnitTester $I)
    {
        $providers = require(appPath('config/providers.php'));

        $I->assertEquals(ConfigProvider::class, $providers[0]);
        $I->assertEquals(EventsManagerProvider::class, $providers[1]);
        $I->assertEquals(LoggerProvider::class, $providers[2]);
        $I->assertEquals(ErrorHandlerProvider::class, $providers[3]);
        $I->assertEquals(ResponseProvider::class, $providers[4]);
        $I->assertEquals(RouterProvider::class, $providers[5]);
    }
}

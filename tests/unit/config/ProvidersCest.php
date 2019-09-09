<?php

namespace Niden\Tests\unit\config;

use Niden\Providers\CliDispatcherProvider;
use Niden\Providers\ConfigProvider;
use Niden\Providers\DatabaseProvider;
use Niden\Providers\ErrorHandlerProvider;
use Niden\Providers\EventsManagerProvider;
use Niden\Providers\LoggerProvider;
use Niden\Providers\ModelsMetadataProvider;
use Niden\Providers\RequestProvider;
use Niden\Providers\ResponseProvider;
use Niden\Providers\RouterProvider;
use UnitTester;
use function Niden\Core\appPath;

class ProvidersCest
{
    public function checkApiProviders(UnitTester $I)
    {
        $providers = require(appPath('api/config/providers.php'));

        $I->assertEquals(ConfigProvider::class, $providers[0]);
        $I->assertEquals(LoggerProvider::class, $providers[1]);
        $I->assertEquals(ErrorHandlerProvider::class, $providers[2]);
        $I->assertEquals(DatabaseProvider::class, $providers[3]);
        $I->assertEquals(ModelsMetadataProvider::class, $providers[4]);
        $I->assertEquals(RequestProvider::class, $providers[5]);
        $I->assertEquals(ResponseProvider::class, $providers[6]);
        $I->assertEquals(RouterProvider::class, $providers[7]);
    }

    public function checkCliProviders(UnitTester $I)
    {
        $providers = require(appPath('cli/config/providers.php'));

        $I->assertEquals(ConfigProvider::class, $providers[0]);
        $I->assertEquals(LoggerProvider::class, $providers[1]);
        $I->assertEquals(ErrorHandlerProvider::class, $providers[2]);
        $I->assertEquals(DatabaseProvider::class, $providers[3]);
        $I->assertEquals(ModelsMetadataProvider::class, $providers[4]);
        $I->assertEquals(CliDispatcherProvider::class, $providers[5]);
    }
}

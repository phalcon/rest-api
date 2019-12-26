<?php

namespace Phalcon\Api\Tests\unit\config;

use Phalcon\Api\Providers\CliDispatcherProvider;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\DatabaseProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Api\Providers\RequestProvider;
use Phalcon\Api\Providers\ResponseProvider;
use Phalcon\Api\Providers\RouterProvider;
use UnitTester;
use function Phalcon\Api\Core\appPath;

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

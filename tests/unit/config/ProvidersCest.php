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

        $I->assertSame(ConfigProvider::class, $providers[0]);
        $I->assertSame(LoggerProvider::class, $providers[1]);
        $I->assertSame(ErrorHandlerProvider::class, $providers[2]);
        $I->assertSame(DatabaseProvider::class, $providers[3]);
        $I->assertSame(ModelsMetadataProvider::class, $providers[4]);
        $I->assertSame(RequestProvider::class, $providers[5]);
        $I->assertSame(ResponseProvider::class, $providers[6]);
        $I->assertSame(RouterProvider::class, $providers[7]);
    }

    public function checkCliProviders(UnitTester $I)
    {
        $providers = require(appPath('cli/config/providers.php'));

        $I->assertSame(ConfigProvider::class, $providers[0]);
        $I->assertSame(LoggerProvider::class, $providers[1]);
        $I->assertSame(ErrorHandlerProvider::class, $providers[2]);
        $I->assertSame(DatabaseProvider::class, $providers[3]);
        $I->assertSame(ModelsMetadataProvider::class, $providers[4]);
        $I->assertSame(CliDispatcherProvider::class, $providers[5]);
    }
}

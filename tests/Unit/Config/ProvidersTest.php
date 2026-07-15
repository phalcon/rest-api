<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Tests\Unit\Config;

use Phalcon\Api\Providers\CliDispatcherProvider;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\DatabaseProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Api\Providers\RequestProvider;
use Phalcon\Api\Providers\ResponseProvider;
use Phalcon\Api\Providers\RouterProvider;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function Phalcon\Api\Core\appPath;

final class ProvidersTest extends AbstractUnitTestCase
{
    public function testApiProviders(): void
    {
        $providers = require(appPath('api/config/providers.php'));

        $this->assertSame(ConfigProvider::class, $providers[0]);
        $this->assertSame(LoggerProvider::class, $providers[1]);
        $this->assertSame(ErrorHandlerProvider::class, $providers[2]);
        $this->assertSame(DatabaseProvider::class, $providers[3]);
        $this->assertSame(ModelsMetadataProvider::class, $providers[4]);
        $this->assertSame(RequestProvider::class, $providers[5]);
        $this->assertSame(ResponseProvider::class, $providers[6]);
        $this->assertSame(RouterProvider::class, $providers[7]);
    }

    public function testCliProviders(): void
    {
        $providers = require(appPath('cli/config/providers.php'));

        $this->assertSame(ConfigProvider::class, $providers[0]);
        $this->assertSame(LoggerProvider::class, $providers[1]);
        $this->assertSame(ErrorHandlerProvider::class, $providers[2]);
        $this->assertSame(DatabaseProvider::class, $providers[3]);
        $this->assertSame(ModelsMetadataProvider::class, $providers[4]);
        $this->assertSame(CliDispatcherProvider::class, $providers[5]);
    }
}

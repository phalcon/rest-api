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

namespace Phalcon\Api\Tests\Unit\Library\Providers;

use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function date_default_timezone_get;

final class ErrorHandlerTest extends AbstractUnitTestCase
{
    public function testRegistration(): void
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new LoggerProvider();
        $provider->register($diContainer);
        $provider = new ErrorHandlerProvider();
        $provider->register($diContainer);

        $config = $diContainer->getShared('config');

        $this->assertSame(date_default_timezone_get(), $config->path('app.timezone'));
        $this->assertSame(ini_get('display_errors'), 'Off');
        $this->assertSame(E_ALL, (int) ini_get('error_reporting'));
    }
}

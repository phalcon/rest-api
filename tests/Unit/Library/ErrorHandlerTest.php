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

namespace Phalcon\Api\Tests\Unit\Library;

use Phalcon\Api\ErrorHandler;
use Phalcon\Api\Logger;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function Phalcon\Api\Core\appPath;

final class ErrorHandlerTest extends AbstractUnitTestCase
{
    public function testLogErrorOnError(): void
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new LoggerProvider();
        $provider->register($diContainer);

        /** @var Config $config */
        $config = $diContainer->getShared('config');
        /** @var Logger $logger */
        $logger  = $diContainer->getShared('logger');
        $handler = new ErrorHandler($logger, $config);

        $handler->handle(1, 'test error', 'file.php', 4);
        $fileName = appPath('storage/logs/api.log');
        $expected = '[ERROR] [#:1]-[L: 4] : test error (file.php)';

        $this->assertFileContentsContains($fileName, $expected);
    }

    public function testLogErrorOnShutdown(): void
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new LoggerProvider();
        $provider->register($diContainer);

        /** @var Config $config */
        $config = $diContainer->getShared('config');
        /** @var Logger $logger */
        $logger  = $diContainer->getShared('logger');
        $handler = new ErrorHandler($logger, $config);

        $handler->shutdown();
        $fileName = appPath('storage/logs/api.log');
        $expected = '[INFO] Shutdown completed';

        $this->assertFileContentsContains($fileName, $expected);
    }
}

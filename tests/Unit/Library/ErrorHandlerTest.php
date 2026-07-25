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
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Config\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Logger\Logger;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function file_exists;
use function file_get_contents;
use function hrtime;
use function Phalcon\Api\Core\appPath;
use function preg_match;
use function str_replace;

final class ErrorHandlerTest extends AbstractUnitTestCase
{
    private string $logFile = '';

    /**
     * The logger appends. Left alone, the file still holds what earlier tests
     * - and earlier runs - wrote, so every assertion here would pass even with
     * the logging removed outright. Start each test from an empty slate.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->logFile = appPath('storage/logs/api.log');

        $this->safeDeleteFile($this->logFile);
    }

    public function testLogErrorOnError(): void
    {
        $handler = new ErrorHandler($this->getLogger(), $this->getConfig(true));

        $result = $handler->handle(1, 'test error', 'file.php', 4);

        $this->assertFileContentsContains(
            $this->logFile,
            '[error] [#:1]-[L: 4] : test error (file.php)'
        );

        /**
         * set_error_handler() reads a falsy return as "not handled" and runs
         * PHP's own handler as well. The error has been logged, so it is.
         */
        $this->assertTrue($result);
    }

    /**
     * Not every caller knows where the error came from; the signature defaults
     * say so, and the log line has to stay readable when they are taken.
     */
    public function testLogErrorOnErrorWithoutFileAndLine(): void
    {
        $handler = new ErrorHandler($this->getLogger(), $this->getConfig(true));

        $handler->handle(2, 'no location');

        $this->assertFileContentsContains(
            $this->logFile,
            '[error] [#:2]-[L: 0] : no location ()'
        );
    }

    public function testLogErrorOnShutdown(): void
    {
        $handler = new ErrorHandler($this->getLogger(), $this->getConfig(true));

        $handler->shutdown();

        $contents = (string) file_get_contents($this->logFile);

        /**
         * The numbers move with the machine, so pin their shape instead: four
         * decimals for the milliseconds, two for the megabytes, which is what
         * the number_format() precision arguments promise.
         */
        $pattern = '/\[info\] Shutdown completed '
            . '\[([\d,]+\.\d{4})\]ms - \[([\d,]+\.\d{2})\]MB/';

        $this->assertMatchesRegularExpression($pattern, $contents);

        preg_match($pattern, $contents, $matches);

        $execution = (float) str_replace(',', '', $matches[1]);
        $memory    = (float) str_replace(',', '', $matches[2]);

        /**
         * Bounds, not values: one division turns nanoseconds into
         * milliseconds, the other bytes into megabytes. Get the operator or
         * the operand wrong and the result lands orders of magnitude away,
         * which is the only part worth asserting on a moving number.
         */
        $this->assertGreaterThanOrEqual(0, $execution);
        $this->assertLessThan(60000, $execution);
        $this->assertLessThan(1000, $memory);
    }

    /**
     * The metrics are a development aid. Outside devMode the shutdown has
     * nothing to say, and saying it anyway would put timings in production
     * logs.
     */
    public function testNoLogOnShutdownWhenNotInDevMode(): void
    {
        $handler = new ErrorHandler($this->getLogger(), $this->getConfig(false));

        $handler->shutdown();

        $contents = true === file_exists($this->logFile)
            ? (string) file_get_contents($this->logFile)
            : '';

        $this->assertStringNotContainsString('Shutdown completed', $contents);
    }

    private function getConfig(bool $devMode): Config
    {
        return new Config(
            [
                'app' => [
                    'devMode' => $devMode,
                    'time'    => hrtime(true),
                ],
            ]
        );
    }

    private function getLogger(): Logger
    {
        $container = new FactoryDefault();

        /**
         * The logger reads its filename and path from `config`, so the config
         * service has to be there first - the provider list registers them in
         * that order for the same reason.
         */
        (new ConfigProvider())->register($container);
        (new LoggerProvider())->register($container);

        /** @var Logger $logger */
        $logger = $container->getShared('logger');

        return $logger;
    }
}

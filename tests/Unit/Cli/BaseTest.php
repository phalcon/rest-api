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

namespace Phalcon\Api\Tests\Unit\Cli;

use Phalcon\Api\Cli\Tasks\MainTask;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function ob_end_clean;
use function ob_get_contents;
use function ob_start;

use const PHP_EOL;

final class BaseTest extends AbstractUnitTestCase
{
    public function testOutput(): void
    {
        $container = new Cli();
        $task      = new MainTask();
        $task->setDI($container);

        ob_start();
        $task->mainAction();
        $actual = ob_get_contents();
        ob_end_clean();

        $year     = date('Y');
        $expected = ""
            . "******************************************************" . PHP_EOL
            . " Phalcon Team | (C) {$year}" . PHP_EOL
            . "******************************************************" . PHP_EOL
            . "" . PHP_EOL
            . "Usage: bin/cli <command>" . PHP_EOL
            . "" . PHP_EOL
            . "  --help         \e[0;32m(safe)\e[0m shows the help screen/available commands" . PHP_EOL
            . "  --clear-cache  \e[0;32m(safe)\e[0m clears the cache folders" . PHP_EOL
            . PHP_EOL;

        $this->assertSame($expected, $actual);
    }
}

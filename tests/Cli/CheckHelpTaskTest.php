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

namespace Phalcon\Api\Tests\Cli;

use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function dirname;
use function exec;
use function implode;

use const PHP_EOL;

/**
 * Ported from the Codeception `CheckHelpTaskCest`. Codeception's Cli module
 * supplied `runShellCommand()`; Talon has no equivalent, so the binary is
 * invoked directly. The path is absolute because the working directory a test
 * runs from is not guaranteed.
 */
final class CheckHelpTaskTest extends AbstractUnitTestCase
{
    public function testCheckHelp(): void
    {
        $output   = [];
        $exitCode = 1;

        exec(dirname(__FILE__, 3) . '/runCli 2>&1', $output, $exitCode);

        $shellOutput = implode(PHP_EOL, $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('--help', $shellOutput);
        $this->assertStringContainsString('--clear-cache', $shellOutput);
    }
}

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

use FilesystemIterator;
use Phalcon\Api\Cli\Tasks\ClearcacheTask;
use Phalcon\Api\Providers\CacheDataProvider;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function fclose;
use function iterator_count;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;
use function Phalcon\Api\Core\appPath;
use function uniqid;

final class ClearCacheTest extends AbstractUnitTestCase
{
    public function testClearCache(): void
    {
        require appPath('vendor/autoload.php');

        $path      = appPath('/storage/cache/data');
        $container = new Cli();
        $config    = new ConfigProvider();
        $config->register($container);
        $cache = new CacheDataProvider();
        $cache->register($container);
        $task = new ClearcacheTask();
        $task->setDI($container);

        $iterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        $count    = iterator_count($iterator);

        $this->createFile();
        $this->createFile();
        $this->createFile();
        $this->createFile();

        $iterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        $this->assertSame($count + 4, iterator_count($iterator));

        ob_start();
        $task->mainAction();
        $actual = ob_get_contents();
        ob_end_clean();

        // Codeception's assertGreaterOrEquals is spelled assertGreaterThanOrEqual in
        // PHPUnit. NOTE: both assertions are vacuous - strpos() returns false when the
        // needle is absent, and false >= 0 is true. assertStringContainsString is almost
        // certainly the intent; left as-is because that is a change of test logic.
        $this->assertGreaterThanOrEqual(0, strpos($actual, 'Clearing Cache folders'));
        $this->assertGreaterThanOrEqual(0, strpos($actual, 'Cleared Cache folders'));

        $iterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        $this->assertSame(1, iterator_count($iterator));
    }

    private function createFile(): void
    {
        $name    = appPath('/storage/cache/data/') . uniqid('tmp_') . '.cache';
        $pointer = fopen($name, 'wb');
        fwrite($pointer, 'test');
        fclose($pointer);
    }
}

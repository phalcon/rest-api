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
use Phalcon\Config\Config;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Redis;
use SplFileInfo;

use function chmod;
use function fclose;
use function file_exists;
use function file_put_contents;
use function fopen;
use function fwrite;
use function iterator_count;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;
use function Phalcon\Api\Core\appPath;
use function uniqid;
use function unlink;

use const PHP_EOL;

final class ClearCacheTest extends AbstractUnitTestCase
{
    /**
     * Tracked files that the task's whitelist deliberately spares. The task
     * clears the real cache tree, so a mutant that drops '.gitignore' from
     * that whitelist deletes them for good: the working tree goes dirty and
     * every later run of this test measures the wreckage instead of the task.
     * Put them back first so each run starts from the same tree.
     */
    private const KEPT_FILES = [
        '/storage/cache/.gitignore',
        '/storage/cache/data/.gitignore',
        '/storage/cache/metadata/.gitignore',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (self::KEPT_FILES as $file) {
            $path = appPath($file);

            if (false === file_exists($path)) {
                file_put_contents($path, '*' . PHP_EOL . '!.gitignore' . PHP_EOL);
                // Restore the tracked mode; recreating them would leave the
                // working tree dirty on the permission bits alone.
                chmod($path, 0755);
            }
        }
    }

    public function testClearCache(): void
    {
        require appPath('vendor/autoload.php');

        $dataPath  = appPath('/storage/cache/data');
        $container = new Cli();
        $config    = new ConfigProvider();
        $config->register($container);
        $cache = new CacheDataProvider();
        $cache->register($container);
        $task = new ClearcacheTask();
        $task->setDI($container);

        /**
         * The task reports counts and prints a dot per item cleared, so the
         * output is only deterministic if the state going in is. Empty the
         * whole tree bar the whitelisted .gitignore files, then seed exactly
         * four cache files.
         */
        $this->emptyCacheTree();
        $this->createFile();
        $this->createFile();
        $this->createFile();
        $this->createFile();

        $redis = $this->connectRedis($container);
        $redis->flushAll();
        $redis->set('api-data-one', 'x');
        $redis->set('api-data-two', 'y');
        // A key outside the api-data namespace the task must leave untouched.
        $redis->set('other-key', 'z');

        ob_start();
        $task->mainAction();
        $actual = ob_get_contents();
        ob_end_clean();

        /**
         * The exact transcript, asserted whole: the phase banners, the two
         * counts, and one dot per item. A bare "contains" check let the
         * newlines wander and the counts drift; this pins every operand of
         * every line down.
         */
        $expected = 'Clearing Cache folders' . PHP_EOL
            . 'Found 4 files' . PHP_EOL
            . '....'
            . PHP_EOL . 'Cleared Cache folders' . PHP_EOL
            . 'Clearing data cache' . PHP_EOL
            . 'Found 3 keys' . PHP_EOL
            . '..'
            . PHP_EOL . 'Cleared data cache' . PHP_EOL;

        $this->assertSame($expected, $actual);

        // The four seeded files are gone; only the .gitignore remains.
        $iterator = new FilesystemIterator($dataPath, FilesystemIterator::SKIP_DOTS);
        $this->assertSame(1, iterator_count($iterator));

        // The api-data keys were deleted, the unrelated key was spared.
        $this->assertSame(0, $redis->exists('api-data-one', 'api-data-two'));
        $this->assertSame(1, $redis->exists('other-key'));

        $redis->flushAll();
    }

    private function connectRedis(Cli $container): Redis
    {
        /** @var Config $config */
        $config  = $container->get('config');
        $options = $config->path('cache')
                          ->toArray()['options']
        ;

        $redis = new Redis();
        $redis->connect($options['host'], (int) $options['port']);

        return $redis;
    }

    private function createFile(): void
    {
        $name    = appPath('/storage/cache/data/') . uniqid('cache_') . '.cache';
        $pointer = fopen($name, 'wb');
        fwrite($pointer, 'test');
        fclose($pointer);
    }

    private function emptyCacheTree(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                appPath('storage/cache'),
                FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (true !== $file->isDir() && '.gitignore' !== $file->getFilename()) {
                unlink($file->getPathname());
            }
        }
    }
}

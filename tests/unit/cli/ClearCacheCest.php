<?php

namespace Niden\Tests\unit\config;

use function fclose;
use function iterator_count;
use function Niden\Core\appPath;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;
use function uniqid;
use FilesystemIterator;
use Niden\Cli\Tasks\ClearcacheTask;
use Phalcon\Di\FactoryDefault\Cli;
use \UnitTester;

class ClearCacheCest
{
    public function checkClearCache(UnitTester $I)
    {
        require appPath('vendor/autoload.php');

        $path      = appPath('/storage/cache/data');
        $container = new Cli();
        $task      = new ClearcacheTask();
        $task->setDI($container);

        $iterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        $count    = iterator_count($iterator);

        $this->createFile();
        $this->createFile();
        $this->createFile();
        $this->createFile();

        $iterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        $I->assertEquals($count + 4, iterator_count($iterator));

        ob_start();
        $task->mainAction();
        $actual = ob_get_contents();
        ob_end_clean();

        $I->assertGreaterOrEquals(0, strpos($actual, 'Clearing Cache folders'));
        $I->assertGreaterOrEquals(0, strpos($actual, 'Cleared Cache folders'));

        $iterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        $I->assertEquals(1, iterator_count($iterator));
    }

    private function createFile()
    {
        $name = appPath('/storage/cache/data/') . uniqid('tmp_') . '.cache';
        $pointer = fopen($name, 'wb');
        fwrite($pointer, 'test');
        fclose($pointer);
    }
}

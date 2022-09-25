<?php

namespace Phalcon\Api\Tests\unit\cli;

use Phalcon\Api\Cli\Tasks\MainTask;
use Phalcon\Di\FactoryDefault\Cli;
use UnitTester;

use function ob_end_clean;
use function ob_get_contents;
use function ob_start;

use const PHP_EOL;

class BaseCest
{
    public function checkOutput(UnitTester $I)
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
            . "Usage: runCli <command>" . PHP_EOL
            . "" . PHP_EOL
            . "  --help         \e[0;32m(safe)\e[0m shows the help screen/available commands" . PHP_EOL
            . "  --clear-cache  \e[0;32m(safe)\e[0m clears the cache folders" . PHP_EOL
            . PHP_EOL;

        $I->assertSame($expected, $actual);
    }
}

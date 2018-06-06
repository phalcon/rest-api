<?php

namespace Niden\Tests\unit\config;

use function ob_end_clean;
use function ob_get_contents;
use function ob_start;
use Niden\Cli\Tasks\MainTask;
use Phalcon\Di\FactoryDefault\Cli;
use \UnitTester;

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

        $actual   = str_replace("\n", "\r\n", $actual);
        $year     = date('Y');
        $expected = <<<EOF
******************************************************
 Phalcon Team | (C) {$year}
******************************************************

Usage: team <command>

  --help         \e[0;32m(safe)\e[0m shows the help screen/available commands
  --clear-cache  \e[0;32m(safe)\e[0m clears the cache folders

EOF;

        $I->assertEquals($expected, $actual);
    }
}

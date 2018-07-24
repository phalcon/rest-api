<?php

namespace Niden\Tests\unit\cli;

use CliTester;
use function Niden\Core\appPath;

class BootstrapCest
{
    public function checkBootstrap(CliTester $I)
    {
        ob_start();
        require appPath('cli/cli.php');
        $actual = ob_get_contents();
        ob_end_clean();

        $actual   = str_replace("\r\n", "\n", $actual);
        $year     = date('Y');
        $expected = <<<EOF
******************************************************
 Phalcon Team | (C) {$year}
******************************************************

Usage: runCli <command>

  --help         \e[0;32m(safe)\e[0m shows the help screen/available commands
  --clear-cache  \e[0;32m(safe)\e[0m clears the cache folders

EOF;

        $I->assertEquals($expected, $actual);
    }
}

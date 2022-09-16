<?php

namespace Phalcon\Api\Tests\unit\cli;

use CliTester;

use function Phalcon\Api\Core\appPath;

use const PHP_EOL;

class BootstrapCest
{
    public function checkBootstrap(CliTester $I)
    {
        ob_start();
        require appPath('cli/cli.php');
        $actual = ob_get_contents();
        ob_end_clean();

        $year     = date('Y');
        $expected = "" // Here just for readability
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

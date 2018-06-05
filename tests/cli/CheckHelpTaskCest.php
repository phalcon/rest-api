<?php

namespace Niden\Tests\cli;

use \CliTester;

class CheckHelpTaskCest
{
    public function checkHelp(CliTester $I)
    {
        $I->runShellCommand('./runCli');
        $I->seeResultCodeIs(0);
        $I->seeInShellOutput('Phalcon Team | (C) ' . date('Y'));
        $I->seeInShellOutput('--help');
        $I->seeInShellOutput('--clear-cache');
    }
}

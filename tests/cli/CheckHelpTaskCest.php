<?php

namespace Niden\Tests\cli;

use \CliTester;

class CheckHelpTaskCest
{

    // tests
    public function tryToTest(CliTester $I)
    {
        $I->runShellCommand('./runCli');
        $I->seeResultCodeIs(0);
        $I->seeInShellOutput('Phalcon Team | (C) ' . date('Y'));
        $I->seeInShellOutput('--help');
        $I->seeInShellOutput('--clear-cache');
    }
}

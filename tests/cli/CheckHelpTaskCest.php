<?php

namespace Niden\Tests\cli;

use \CliTester;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;

class CheckHelpTaskCest
{
    public function checkHelp(CliTester $I)
    {
//        /** @var Console $application */
//        $application = $I->grabServiceFromDi('application');
//        /** @var Dispatcher $dispatcher */
//        $dispatcher  = $I->grabServiceFromDi('dispatcher');
//
//        $application->setArgument(['--help'], false)->handle();
//        $I->assertEquals($dispatcher->getReturnedValue(), 'aaa');

        $I->runShellCommand('./runCli');
        $I->seeResultCodeIs(0);
        $I->seeInShellOutput('Phalcon Team | (C) ' . date('Y'));
        $I->seeInShellOutput('--help');
        $I->seeInShellOutput('--clear-cache');
    }
}

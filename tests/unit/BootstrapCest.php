<?php

namespace Niden\Tests\unit;

use \CliTester;
use function Niden\Core\appPath;

class BootstrapCest
{
    public function checkBootstrap(CliTester $I)
    {
        ob_start();
        require appPath('api/public/index.php');
        $actual = ob_get_contents();
        ob_end_clean();

        $results = json_decode($actual, true);
        $I->assertEquals('1.0', $results['jsonapi']['version']);
        $I->assertEmpty($results['data']);
        $I->assertEquals(3000, $results['errors']['code']);
        $I->assertEquals('404 Not Found', $results['errors']['detail']);
    }
}

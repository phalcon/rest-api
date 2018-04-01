<?php

namespace Niden\Tests\unit\config;

use function Niden\Functions\appPath;
use \UnitTester;

class ProvidersCest
{
    public function checkProvidersArray(UnitTester $I)
    {
        $providers = require_once(appPath('config/providers.php'));

        $I->assertTrue(is_array($providers));
        $I->assertEquals(0, count($providers));
    }
}

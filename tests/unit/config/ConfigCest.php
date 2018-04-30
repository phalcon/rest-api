<?php

namespace Niden\Tests\unit\config;

use function apache_setenv;
use function is_array;
use function Niden\Functions\appPath;
use function Niden\Functions\envValue;
use \UnitTester;

class ConfigCest
{
    public function checkConfig(UnitTester $I)
    {
        $config = require(appPath('config/config.php'));

        $I->assertTrue(is_array($config));
        $I->assertTrue(isset($config['app']));
        $I->assertTrue(isset($config['db']));
        $I->assertTrue(isset($config['cache']));
        $I->assertTrue(isset($config['logger']));
    }
}

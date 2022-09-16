<?php

namespace Phalcon\Api\Tests\unit\config;

use UnitTester;

use function is_array;
use function Phalcon\Api\Core\appPath;

class ConfigCest
{
    public function checkConfig(UnitTester $I)
    {
        $config = require(appPath('library/Core/config.php'));

        $I->assertTrue(is_array($config));
        $I->assertTrue(isset($config['app']));
        $I->assertTrue(isset($config['cache']));
    }
}

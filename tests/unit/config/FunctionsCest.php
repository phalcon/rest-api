<?php

namespace Niden\Tests\unit\config;

use function Niden\Functions\appPath;
use function Niden\Functions\envValue;
use \UnitTester;

class FunctionsCest
{
    public function checkApppath(UnitTester $I)
    {
        $path = dirname(dirname(dirname(__DIR__)));
        $I->assertEquals($path, appPath());
    }

    public function checkApppathWithParameter(UnitTester $I)
    {
        $path = dirname(dirname(dirname(__DIR__))) . '/config/config.php';
        $I->assertEquals($path, appPath('config/config.php'));
    }

    public function checkEnvvalueAsFalse(UnitTester $I)
    {
        putenv('SOMEVAL=false');
        $I->assertFalse(envValue('SOMEVAL'));
    }

    public function checkEnvvalueAsTrue(UnitTester $I)
    {
        putenv('SOMEVAL=true');
        $I->assertTrue(envValue('SOMEVAL'));
    }

    public function checkEnvvalueWithValue(UnitTester $I)
    {
        putenv('SOMEVAL=someval');
        $I->assertEquals('someval', envValue('SOMEVAL'));
    }
}

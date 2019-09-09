<?php

namespace Phalcon\Api\Tests\unit\config;

use Phalcon\Api\Constants\Relationships;
use UnitTester;
use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\appUrl;
use function Phalcon\Api\Core\envValue;

class FunctionsCest
{
    public function checkApppath(UnitTester $I)
    {
        $path = dirname(dirname(dirname(__DIR__)));
        $I->assertEquals($path, appPath());
    }

    public function checkApppathWithParameter(UnitTester $I)
    {
        $path = dirname(dirname(dirname(__DIR__))) . '/library/Core/config.php';
        $I->assertEquals($path, appPath('library/Core/config.php'));
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

    public function checkEnvurlWithUrl(UnitTester $I)
    {
        $I->assertEquals(
            'http://api.phalcon.ld/companies/1',
            appUrl(Relationships::COMPANIES, 1)
        );
    }
}

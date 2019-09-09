<?php

namespace Phalcon\Api\Tests\unit\config;

use Niden\Constants\Relationships;
use UnitTester;
use function Niden\Core\appPath;
use function Niden\Core\appUrl;
use function Niden\Core\envValue;

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

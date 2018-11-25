<?php

namespace Gewaer\Tests\unit\library\Constants;

use CliTester;
use Gewaer\Constants\Flags;

class FlagsCest
{
    public function checkConstants(CliTester $I)
    {
        $I->assertEquals(1, Flags::ACTIVE);
        $I->assertEquals(2, Flags::INACTIVE);
        $I->assertEquals('production', Flags::PRODUCTION);
        $I->assertEquals('development', Flags::DEVELOPMENT);
    }
}

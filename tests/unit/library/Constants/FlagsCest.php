<?php

namespace Phalcon\Api\Tests\unit\library\Constants;

use CliTester;
use Phalcon\Api\Constants\Flags;

class FlagsCest
{
    public function checkConstants(CliTester $I)
    {
        $I->assertSame(1, Flags::ACTIVE);
        $I->assertSame(2, Flags::INACTIVE);
    }
}

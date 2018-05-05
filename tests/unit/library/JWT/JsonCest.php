<?php

namespace Niden\Tests\unit;

use Niden\JWT\Base;
use Niden\JWT\Claims;
use Niden\JWT\Exception\DomainException;
use \UnitTester;

class JsonCest
{
    public function checkJsonEncode(UnitTester $I)
    {
        $jwt = new Base();

        $I->assertEquals('{"a":"b"}', $jwt->jsonEncode(['a' => 'b']));
        $I->assertEquals('a', $jwt->jsonEncode('a'));
    }

    public function checkJsonThrowsExceptionWithUTF8(UnitTester $I)
    {
        $I->expectException(
            DomainException::class,
            function () {
                $jwt   = new Base();
                $input = "\xB1\x31";
                $jwt->jsonEncode($input);
            }
        );
    }
}

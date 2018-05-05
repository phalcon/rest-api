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
        $I->assertEquals('"a"', $jwt->jsonEncode('a'));
    }

    public function checkJsonEncodeThrowsExceptionWithUTF8(UnitTester $I)
    {
        $I->expectException(
            new DomainException('Malformed UTF-8 characters'),
            function () {
                $jwt   = new Base();
                $input = "\xB1\x31";
                $jwt->jsonEncode($input);
            }
        );
    }

    public function checkJsonDecode(UnitTester $I)
    {
        $jwt = new Base();

        $I->assertEquals(['a' => 'b'], $jwt->jsonDecode('{"a":"b"}', true));
        $I->assertEquals('a', $jwt->jsonDecode('"a"'));
    }

    public function checkJsonDecodeThrowsExceptionWithMalformedJson(UnitTester $I)
    {
        $I->expectException(
            new DomainException('Syntax error, malformed JSON'),
            function () {
                $jwt   = new Base();
                $input = '{"a"';
                $jwt->jsonDecode($input, true);
            }
        );
    }
}

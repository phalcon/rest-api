<?php

namespace Niden\Tests\unit;

use Niden\JWT\Base;
use Niden\JWT\Exception;
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
            new Exception(
                'json_decode error: Malformed UTF-8 characters, possibly incorrectly encoded'
            ),
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
            new Exception('json_decode error: Syntax error'),
            function () {
                $jwt   = new Base();
                $input = '{"a"';
                $jwt->jsonDecode($input, true);
            }
        );
    }
}

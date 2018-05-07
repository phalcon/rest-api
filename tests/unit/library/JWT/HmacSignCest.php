<?php

namespace Niden\Tests\unit;

use Niden\JWT\Hmac;
use Niden\JWT\Claims;
use Niden\JWT\Exception;
use \UnitTester;
use function unpack;

class HmacSignCest
{
    /**
     * @param UnitTester $I
     */
    public function signThrowsExceptionWithUnknownCipher(UnitTester $I)
    {
        $I->expectException(
            new Exception(
                'Cipher not supported'
            ),
            function () {
                $jwt = new Hmac();
                $jwt->sign('message', '1234', 'abc');
            }
        );
    }

    /**
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function checkSign(UnitTester $I)
    {
        $jwt = new Hmac();

        $expected = 'b260e06202042942914d5c279307f8e07ad3a736cf0285c55bc833d1388e4910';
        $actual   = $jwt->sign('My Message', '1234567890', Claims::JWT_CIPHER_HS256);
        $actual   = unpack('H*', $actual);
        $I->assertEquals($expected, $actual[1]);
    }
}

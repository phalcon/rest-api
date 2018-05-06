<?php

namespace Niden\Tests\unit;

use Niden\JWT\Base;
use Niden\JWT\Claims;
use Niden\JWT\Exception;
use \UnitTester;
use function unpack;

class SignCest
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
                $jwt = new Base();
                $jwt->sign('message', '1234', 'abc');
            }
        );
    }

    /**
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function signWithHmac(UnitTester $I)
    {
        $jwt = new Base();

        $expected = 'b260e06202042942914d5c279307f8e07ad3a736cf0285c55bc833d1388e4910';
        $actual   = $jwt->sign('My Message', '1234567890', Claims::JWT_CIPHER_HS256);
        $actual   = unpack('H*', $actual);
        $I->assertEquals($expected, $actual[1]);
    }

    /**
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function signWithOpenssl(UnitTester $I)
    {
        $I->expectException(
            new Exception(
                'OpenSSL unable to sign data'
            ),
            function () {
                $jwt = new Base();
                $jwt->sign('message', '1234', Claims::JWT_CIPHER_RS256);
            }
        );
    }
}

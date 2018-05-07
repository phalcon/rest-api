<?php

namespace Niden\Tests\unit;

use Niden\JWT\Hmac;
use Niden\JWT\Claims;
use \UnitTester;

class AlgorithmsCest
{
    public function checkSupportedAlgorithms(UnitTester $I)
    {
        $jwt      = new Hmac();
        $expected = [
            Claims::JWT_CIPHER_HS256,
            Claims::JWT_CIPHER_HS384,
            Claims::JWT_CIPHER_HS512,
            Claims::JWT_CIPHER_RS256,
            Claims::JWT_CIPHER_RS384,
            Claims::JWT_CIPHER_RS512,
            Claims::JWT_CIPHER_NONE,
        ];

        $I->assertEquals($expected, $jwt->getSupportedCiphers());
    }

    public function checkIfAnAlgorithmIsSupported(UnitTester $I)
    {
        $jwt = new Hmac();

        $I->assertTrue($jwt->isCipherSupported(Claims::JWT_CIPHER_HS256));
        $I->assertFalse($jwt->isCipherSupported('something random'));
    }
}

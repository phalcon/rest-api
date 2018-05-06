<?php

namespace Niden\Tests\unit;

use Niden\JWT\Base;
use Niden\JWT\Claims;
use \UnitTester;

class AlgorithmsCest
{
    public function checkSupportedAlgorithms(UnitTester $I)
    {
        $jwt      = new Base();
        $expected = [
            Claims::JWT_CIPHER_HS256,
            Claims::JWT_CIPHER_HS384,
            Claims::JWT_CIPHER_HS512,
            Claims::JWT_CIPHER_RS256,
            Claims::JWT_CIPHER_RS384,
            Claims::JWT_CIPHER_RS512,
            Claims::JWT_CIPHER_NONE,
        ];

        $I->assertEquals($expected, $jwt->getSupportedAlgorithms());
    }

    public function checkIfAnAlgorithmIsSupported(UnitTester $I)
    {
        $jwt = new Base();

        $I->assertTrue($jwt->isAlgorithmSupported(Claims::JWT_CIPHER_HS256));
        $I->assertFalse($jwt->isAlgorithmSupported('something random'));
    }
}

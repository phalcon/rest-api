<?php

namespace Niden\Tests\unit;

use Niden\JWT\Base;
use Niden\JWT\Claims;
use function strtr;
use \UnitTester;

class BaseCest
{
    public function checkSupportedAlgorithms(UnitTester $I)
    {
        $jwt      = new Base();
        $expected = [
            Claims::JWT_ALGORITHM_HS256,
            Claims::JWT_ALGORITHM_HS384,
            Claims::JWT_ALGORITHM_HS512,
            Claims::JWT_ALGORITHM_RS256,
            Claims::JWT_ALGORITHM_RS384,
            Claims::JWT_ALGORITHM_RS512,
            Claims::JWT_ALGORITHM_NONE,
        ];

        $I->assertEquals($expected, $jwt->getSupportedAlgorithms());
    }

    public function checkIfAnAlgorithmIsSupported(UnitTester $I)
    {
        $jwt = new Base();

        $I->assertTrue($jwt->isAlgorithmSupported(Claims::JWT_ALGORITHM_HS256));
        $I->assertFalse($jwt->isAlgorithmSupported('something random'));
    }

    public function checkUrlSafeBase64Encode(UnitTester $I)
    {
        $jwt      = new Base();
        $url      = 'https://phalconphp.com/params?some=1&other=b324&a=x554';
        $expected = 'aHR0cHM6Ly9waGFsY29ucGhwLmNvbS9wYXJhbXM/c29tZT0xJm90aGVyPWIzMjQmYT14NTU0';

        $I->assertEquals($expected, $jwt->urlSafeBase64Encode($url));
    }

    public function checkUrlSafeBase64Decode(UnitTester $I)
    {
        $jwt      = new Base();
        $input    = 'aHR0cHM6Ly9waGFsY29ucGhwLmNvbS9wYXJhbXM/c29tZT0xJm90aGVyPWIzMjQmYT14NTU0';
        $expected = 'https://phalconphp.com/params?some=1&other=b324&a=x554';

        $I->assertEquals($expected, $jwt->urlSafeBase64Decode($input));
    }
}

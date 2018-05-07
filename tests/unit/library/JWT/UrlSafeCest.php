<?php

namespace Niden\Tests\unit;

use Niden\JWT\Hmac;
use \UnitTester;

class UrlSafeCest
{
    public function checkUrlSafeBase64Encode(UnitTester $I)
    {
        $jwt      = new Hmac();
        $url      = 'https://phalconphp.com/params?some=1&other=b324&a=x554';
        $expected = 'aHR0cHM6Ly9waGFsY29ucGhwLmNvbS9wYXJhbXM/c29tZT0xJm90aGVyPWIzMjQmYT14NTU0';

        $I->assertEquals($expected, $jwt->urlSafeBase64Encode($url));
    }

    public function checkUrlSafeBase64Decode(UnitTester $I)
    {
        $jwt      = new Hmac();
        $input    = 'aHR0cHM6Ly9waGFsY29ucGhwLmNvbS9wYXJhbXM/c29tZT0xJm90aGVyPWIzMjQmYT14NTU0';
        $expected = 'https://phalconphp.com/params?some=1&other=b324&a=x554';

        $I->assertEquals($expected, $jwt->urlSafeBase64Decode($input));
    }
}

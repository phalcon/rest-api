<?php

namespace Niden\Tests\unit;

use Niden\JWT\Claims;
use \UnitTester;

class ClaimsCest
{
    public function checkConstants(UnitTester $I)
    {
        $I->assertEquals('iss', Claims::JWT_ISSUER);
        $I->assertEquals('sub', Claims::JWT_SUBJECT);
        $I->assertEquals('aud', Claims::JWT_AUDIENCE);
        $I->assertEquals('exp', Claims::JWT_EXPIRATION_TIME);
        $I->assertEquals('nbf', Claims::JWT_NOT_BEFORE);
        $I->assertEquals('iat', Claims::JWT_ISSUED_AT);
        $I->assertEquals('jti', Claims::JWT_ID);

        $I->assertEquals('HS256', Claims::JWT_CIPHER_HS256);
        $I->assertEquals('HS384', Claims::JWT_CIPHER_HS384);
        $I->assertEquals('HS512', Claims::JWT_CIPHER_HS512);
        $I->assertEquals('RS256', Claims::JWT_CIPHER_RS256);
        $I->assertEquals('RS384', Claims::JWT_CIPHER_RS384);
        $I->assertEquals('RS512', Claims::JWT_CIPHER_RS512);
        $I->assertEquals('none', Claims::JWT_CIPHER_NONE);

        $I->assertEquals(7, count(Claims::JWT_CIPHERS));

        $I->assertEquals(['hmac', 'SHA256'], Claims::JWT_CIPHERS[Claims::JWT_CIPHER_HS256]);
        $I->assertEquals(['hmac', 'SHA384'], Claims::JWT_CIPHERS[Claims::JWT_CIPHER_HS384]);
        $I->assertEquals(['hmac', 'SHA512'], Claims::JWT_CIPHERS[Claims::JWT_CIPHER_HS512]);
        $I->assertEquals(['openssl', 'SHA256'], Claims::JWT_CIPHERS[Claims::JWT_CIPHER_RS256]);
        $I->assertEquals(['openssl', 'SHA384'], Claims::JWT_CIPHERS[Claims::JWT_CIPHER_RS384]);
        $I->assertEquals(['openssl', 'SHA512'], Claims::JWT_CIPHERS[Claims::JWT_CIPHER_RS512]);
        $I->assertEquals(['none', ''], Claims::JWT_CIPHERS[Claims::JWT_CIPHER_NONE]);
    }
}

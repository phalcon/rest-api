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
        $I->assertEquals('SHA1', Claims::JWT_CIPHER_SHA1);

        $I->assertEquals(3, count(Claims::JWT_CIPHERS_HMAC));
        $I->assertEquals(4, count(Claims::JWT_CIPHERS_OPENSSL));

        $I->assertEquals('SHA256', Claims::JWT_CIPHERS_HMAC[Claims::JWT_CIPHER_HS256]);
        $I->assertEquals('SHA384', Claims::JWT_CIPHERS_HMAC[Claims::JWT_CIPHER_HS384]);
        $I->assertEquals('SHA512', Claims::JWT_CIPHERS_HMAC[Claims::JWT_CIPHER_HS512]);
        $I->assertEquals(OPENSSL_ALGO_SHA256, Claims::JWT_CIPHERS_OPENSSL[Claims::JWT_CIPHER_RS256]);
        $I->assertEquals(OPENSSL_ALGO_SHA384, Claims::JWT_CIPHERS_OPENSSL[Claims::JWT_CIPHER_RS384]);
        $I->assertEquals(OPENSSL_ALGO_SHA512, Claims::JWT_CIPHERS_OPENSSL[Claims::JWT_CIPHER_RS512]);
    }
}

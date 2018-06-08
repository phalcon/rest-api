<?php

namespace Niden\Tests\unit;

use \CliTester;
use Niden\JWTClaims;

class JWTClaimsCest
{
    public function checkConstants(CliTester $I)
    {
        $I->assertEquals('jti', JWTClaims::CLAIM_ID);
        $I->assertEquals('iss', JWTClaims::CLAIM_ISSUER);
    }
}

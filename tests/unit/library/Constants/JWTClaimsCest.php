<?php

namespace Niden\Tests\unit\library\Constants;

use CliTester;
use Niden\Constants\JWTClaims;

class JWTClaimsCest
{
    public function checkConstants(CliTester $I)
    {
        $I->assertEquals('jti', JWTClaims::CLAIM_ID);
        $I->assertEquals('iss', JWTClaims::CLAIM_ISSUER);
    }
}

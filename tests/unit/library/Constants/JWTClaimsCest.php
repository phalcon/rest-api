<?php

namespace Phalcon\Api\Tests\unit\library\Constants;

use CliTester;
use Phalcon\Api\Constants\JWTClaims;

class JWTClaimsCest
{
    public function checkConstants(CliTester $I)
    {
        $I->assertEquals('jti', JWTClaims::CLAIM_ID);
        $I->assertEquals('iss', JWTClaims::CLAIM_ISSUER);
    }
}

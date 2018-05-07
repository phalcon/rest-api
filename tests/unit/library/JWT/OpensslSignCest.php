<?php

namespace Niden\Tests\unit;

use Niden\JWT\Claims;
use Niden\JWT\Exception;
use Niden\JWT\Openssl;
use \UnitTester;

class OpensslSignCest
{
    /**
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function checkSign(UnitTester $I)
    {
        $I->expectException(
            new Exception(
                'OpenSSL unable to sign data'
            ),
            function () {
                $jwt = new Openssl();
                $jwt->sign('message', '1234', Claims::JWT_CIPHER_RS256);
            }
        );
    }
}

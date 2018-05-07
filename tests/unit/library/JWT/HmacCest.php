<?php

namespace Niden\Tests\unit;

use function unpack;
use Niden\JWT\Hmac;
use Niden\JWT\Claims;
use Niden\JWT\Exception;
use \UnitTester;

class HmacCest
{
    private $message = 'Phalcon is the fastest full stack PHP framework!';
    private $secret  = '123abc456def789ghi';

    /**
     * @param UnitTester $I
     */
    public function signThrowsExceptionWithUnknownCipher(UnitTester $I)
    {
        $I->expectException(
            new Exception(
                'Cipher not supported'
            ),
            function () {
                $jwt = new Hmac();
                $jwt->sign($this->message, $this->secret, 'abc');
            }
        );
    }

    /**
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function checkSign(UnitTester $I)
    {
        $jwt = new Hmac();

        $expected = '0c40da2b82b384ddd94c1f0d0997a6c96c7706ad3abda82f31669c6e32d3a14c';
        $actual   = $jwt->sign($this->message, $this->secret, Claims::JWT_CIPHER_HS256);
        $actual   = unpack('H*', $actual);
        $I->assertEquals($expected, $actual[1]);
    }

    /**
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function checkVerify(UnitTester $I)
    {
        $jwt = new Hmac();

        $signature = $jwt->sign($this->message, $this->secret, Claims::JWT_CIPHER_HS256);
        $I->assertTrue($jwt->verify($signature, $this->message, $this->secret, Claims::JWT_CIPHER_HS256));
    }
}

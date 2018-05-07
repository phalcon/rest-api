<?php

namespace Niden\Tests\unit;

use Niden\JWT\Claims;
use Niden\JWT\Exception;
use Niden\JWT\Openssl;
use \UnitTester;

class OpensslCest
{
    private $message = 'Phalcon is the fastest full stack PHP framework!';

    /**
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function checkSignThrowsExceptionWithInvalidKey(UnitTester $I)
    {
        $I->expectException(
            new Exception(
                'OpenSSL unable to sign data'
            ),
            function () {
                $jwt = new Openssl();
                $jwt->sign($this->message, '12345', Claims::JWT_CIPHER_RS256);
            }
        );
    }

    /**
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function checkSignAndVerify(UnitTester $I)
    {
        $jwt = new Openssl();

        $signature = $jwt->sign($this->message, $this->getPrivateKey(), 'SHA1');
        $I->assertTrue($jwt->verify($signature, $this->message, $this->getPublicKey(), 'SHA1'));
    }

    /**
     * A private key for testing purposes
     *
     * @return string
     */
    private function getPrivateKey(): string
    {
        return <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIIBOgIBAAJBANDiE2+Xi/WnO+s120NiiJhNyIButVu6zxqlVzz0wy2j4kQVUC4Z
RZD80IY+4wIiX2YxKBZKGnd2TtPkcJ/ljkUCAwEAAQJAL151ZeMKHEU2c1qdRKS9
sTxCcc2pVwoAGVzRccNX16tfmCf8FjxuM3WmLdsPxYoHrwb1LFNxiNk1MXrxjH3R
6QIhAPB7edmcjH4bhMaJBztcbNE1VRCEi/bisAwiPPMq9/2nAiEA3lyc5+f6DEIJ
h1y6BWkdVULDSM+jpi1XiV/DevxuijMCIQCAEPGqHsF+4v7Jj+3HAgh9PU6otj2n
Y79nJtCYmvhoHwIgNDePaS4inApN7omp7WdXyhPZhBmulnGDYvEoGJN66d0CIHra
I2SvDkQ5CmrzkW5qPaE2oO7BSqAhRZxiYpZFb5CI
-----END RSA PRIVATE KEY-----
EOD;
    }

    private function getPublicKey(): string
    {
        return <<<EOD
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBANDiE2+Xi/WnO+s120NiiJhNyIButVu6
zxqlVzz0wy2j4kQVUC4ZRZD80IY+4wIiX2YxKBZKGnd2TtPkcJ/ljkUCAwEAAQ==
-----END PUBLIC KEY-----
EOD;
    }
}

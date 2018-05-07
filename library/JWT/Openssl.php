<?php

namespace Niden\JWT;

use function extension_loaded;
use function openssl_sign;
use function openssl_verify;

class Openssl extends AbstractJWT
{
    /**
     * OpensslSignCest constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        if (true !== extension_loaded('openssl')) {
            throw new Exception('This module requires the "openssl" PHP extension');
        }
    }

    /**
     * Sign a string given a key and an cipher
     *
     * @param string $message
     * @param string $key
     * @param string $cipher
     *
     * @return string|null
     * @throws Exception
     */
    public function sign(string $message, string $key, string $cipher = Claims::JWT_CIPHER_HS256)
    {
        $signature = '';
        $success   = openssl_sign($message, $signature, $key, $this->cipherToOpenssl($cipher));
        if (true !== $success) {
            throw new Exception('OpenSSL unable to sign data');
        }

        return $signature;
    }

    /**
     * @param string $signature
     * @param string $message
     * @param string $key
     * @param string $cipher
     *
     * @return bool
     * @throws Exception
     */
    public function verify(
        string $signature,
        string $message,
        string $key,
        string $cipher = Claims::JWT_CIPHER_HS256
    ): bool {
        $this->checkCipherSupport($cipher);

        $success = openssl_verify($message, $signature, $key, $cipher);

        if ($success < 0) {
            throw new Exception('OpenSSL error: ' . openssl_error_string());
        }

        return (1 === $success);
    }

    /**
     * Converts a passed string cipher to the OPENSSL numeric constant
     *
     * @param string $cipher
     *
     * @return int
     */
    private function cipherToOpenssl(string $cipher): int
    {
        $ciphers = [
            'SHA1'   => OPENSSL_ALGO_SHA1,
            'MD5'    => OPENSSL_ALGO_MD5,
            'MD4'    => OPENSSL_ALGO_MD4,
            'MD2'    => OPENSSL_ALGO_MD2,
            'DSS1'   => OPENSSL_ALGO_DSS1,
            'SHA224' => OPENSSL_ALGO_SHA224,
            'SHA256' => OPENSSL_ALGO_SHA256,
            'SHA384' => OPENSSL_ALGO_SHA384,
            'SHA512' => OPENSSL_ALGO_SHA512,
            'RMD160' => OPENSSL_ALGO_RMD160,
        ];

        return $ciphers[$cipher] ?? OPENSSL_ALGO_SHA256;
    }
}

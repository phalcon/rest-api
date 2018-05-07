<?php

namespace Niden\JWT;

use function boolval;
use function openssl_sign;
use function openssl_verify;

class Openssl extends AbstractJWT
{
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
    public function sign(string $message, string $key, string $cipher = Claims::JWT_CIPHER_RS256)
    {
        $signature  = '';
        $signCipher = $this->checkCipher($cipher);
        $success    = openssl_sign($message, $signature, $key, $signCipher);
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
        $cipher = Claims::JWT_CIPHER_RS256
    ): bool {
        $signCipher = $this->checkCipher($cipher);
        return boolval(openssl_verify($message, $signature, $key, $signCipher));
    }

    /**
     * Returns the supported ciphers
     *
     * @return array
     */
    protected function getCiphers(): array
    {
        return Claims::JWT_CIPHERS_OPENSSL;
    }
}

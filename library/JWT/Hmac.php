<?php

namespace Niden\JWT;

class Hmac extends AbstractJWT
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
    public function sign(string $message, string $key, string $cipher = Claims::JWT_CIPHER_HS256)
    {
        $signCipher = $this->checkCipher($cipher);

        return hash_hmac($signCipher, $message, $key, true);
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
        $signCipher = $this->checkCipher($cipher);
        $hash       = hash_hmac($signCipher, $message, $key, true);

        return hash_equals($signature, $hash);
    }

    /**
     * Checks cipher support and returns the actual cipher name
     *
     * @param string $cipher
     *
     * @return string
     * @throws Exception
     */
    private function checkCipher(string $cipher): string
    {
        $this->checkCipherSupport($cipher);

        return Claims::JWT_CIPHERS[$cipher][1];
    }
}

<?php

namespace Niden\JWT;

use const JSON_BIGINT_AS_STRING;
use const JSON_ERROR_NONE;
use function array_keys;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function strtoupper;

abstract class AbstractJWT
{
    /**
     * Returns an array of all supported algoritms
     *
     * @return array
     */
    public function getSupportedCiphers(): array
    {
        return array_keys(Claims::JWT_CIPHERS);
    }

    /**
     * Checks if the cipher supplied is supported or not
     *
     * @param string $name
     *
     * @return bool
     */
    public function isCipherSupported(string $name): bool
    {
        $cipher = strtoupper($name);

        return isset(Claims::JWT_CIPHERS[$cipher]);
    }

    /**
     * Decode a JSON string to a PHP object/array
     *
     * @param string $input
     * @param bool   $assoc
     * @param int    $depth
     * @param int    $options
     *
     * @return mixed
     * @throws Exception
     */
    public function jsonDecode(
        string $input,
        bool $assoc = true,
        int $depth = 512,
        int $options = JSON_BIGINT_AS_STRING
    ) {
        $result = json_decode($input, $assoc, $depth, $options);
        $this->processJsonError();

        return $result;
    }

    /**
     * Encode a PHP object into a JSON string
     *
     * @param object|array $input
     * @param int          $options
     * @param int          $depth
     *
     * @return string
     * @throws Exception
     */
    public function jsonEncode($input, int $options = JSON_BIGINT_AS_STRING, int $depth = 512): string
    {
        $result = json_encode($input, $options, $depth);
        $this->processJsonError();

        return $result;
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
    abstract public function sign(string $message, string $key, string $cipher = Claims::JWT_CIPHER_HS256);

    /**
     * Decodes a string encoded with the urlSafeBase64Encode function
     *
     * @param string $string
     *
     * @return string
     */
    public function urlSafeBase64Decode(string $string): string
    {
        return base64_decode(strtr($string, ['-_|' => '+/=']));
    }

    /**
     * Encodes a string with base64 keeping it URL safe
     *
     * @param string $string
     *
     * @return string
     */
    public function urlSafeBase64Encode(string $string): string
    {
        return strtr(base64_encode($string), ['+/=' => '-_|']);
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
    abstract public function verify(
        string $signature,
        string $message,
        string $key,
        $cipher = Claims::JWT_CIPHER_HS256
    ) : bool;

    /**
     * Checks if a cipher is supported and throws an exception if it does not
     *
     * @param string $cipher
     *
     * @return void
     * @throws Exception
     */
    protected function checkCipherSupport(string $cipher)
    {
        if (true !== $this->isCipherSupported($cipher)) {
            throw new Exception('Cipher not supported');
        }
    }

    /**
     * If there was an error, throw an exception with the message
     *
     * @throws Exception
     */
    protected function processJsonError()
    {
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(
                'json_decode error: ' . json_last_error_msg()
            );
        }
    }
}

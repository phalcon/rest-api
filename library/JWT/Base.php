<?php

namespace Niden\JWT;

use function array_keys;
use function json_decode;
use function json_encode;
use const JSON_ERROR_NONE;
use function json_last_error;
use Niden\JWT\Exception\DomainException;
use function strtoupper;
use Niden\JWT\Claims;

class Base
{
    /** @var int */
    private $timeDrift = 0;
    /** @var int */
    private $timestamp = 0;

    /**
     * Checks if the algorithm supplied is supported or not
     *
     * @param string $name
     *
     * @return bool
     */
    public function isAlgorithmSupported(string $name): bool
    {
        $algorithm  = strtoupper($name);
        $algorithms = Claims::JWT_ALGORITHMS;

        return isset($algorithms[$algorithm]);
    }

    /**
     * Decode a JSON string to a PHP object/array
     *
     * @param string $input
     * @param bool   $assoc
     *
     * @return mixed
     * @throws DomainException
     */
    public function jsonDecode(string $input, bool $assoc = false)
    {
        $result    = json_decode($input, $assoc, 512, JSON_BIGINT_AS_STRING);
        $jsonError = json_last_error();

        if (JSON_ERROR_NONE !== $jsonError) {
            $this->processJsonError($jsonError);
        }

        return $result;
    }

    /**
     * Encode a PHP object into a JSON string
     *
     * @param object|array $input
     *
     * @return string
     * @throws DomainException
     */
    public function jsonEncode($input): string
    {
        $result    = json_encode($input);
        $jsonError = json_last_error();

        if (JSON_ERROR_NONE !== $jsonError) {
            $this->processJsonError($jsonError);
        }

        return $result;
    }

    /**
     * Returns an array of all supported algoritms
     *
     * @return array
     */
    public function getSupportedAlgorithms(): array
    {
        return array_keys(Claims::JWT_ALGORITHMS);
    }

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
     * Translates json_last_error to a human readable form
     *
     * @param int $jsonError
     *
     * @throws DomainException
     */
    private function processJsonError(int $jsonError)
    {
        $messages  = [
            JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
            JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8           => 'Malformed UTF-8 characters',
        ];

        $return = $messages[$jsonError] ?? 'Unknown JSON error: ' . $jsonError;

        throw new DomainException($return);
    }
}

<?php

namespace Niden\JWT;

use function array_keys;
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
}

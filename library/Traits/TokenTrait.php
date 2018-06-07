<?php

namespace Niden\Traits;

use function Niden\Core\envValue;
use function time;

/**
 * Trait TokenTrait
 *
 * @package Niden\Traits
 */
trait TokenTrait
{
    /**
     * Returns the default audience for the tokens
     *
     * @return string
     */
    protected function getTokenAudience(): string
    {
        return envValue('TOKEN_AUDIENCE', 'https://phalconphp.com');
    }

    /**
     * Returns the time the token is issued at
     *
     * @return int
     */
    protected function getTokenTimeIssuedAt(): int
    {
        return time();
    }

    /**
     * Returns the time drift i.e. token will be valid not before
     *
     * @return int
     */
    protected function getTokenTimeNotBefore(): int
    {
        return (time() + envValue('TOKEN_NOT_BEFORE', 10));
    }

    /**
     * Returns the expiry time for the token
     *
     * @return int
     */
    protected function getTokenTimeExpiration(): int
    {
        return (time() + envValue('TOKEN_EXPIRATION', 3600));
    }
}

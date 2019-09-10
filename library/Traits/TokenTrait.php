<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Traits;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use function Phalcon\Api\Core\envValue;
use function time;

/**
 * Trait TokenTrait
 */
trait TokenTrait
{
    /**
     * Returns the JWT token object
     *
     * @param string $token
     *
     * @return Token
     */
    protected function getToken(string $token): Token
    {
        return (new Parser())->parse($token);
    }

    /**
     * Returns the default audience for the tokens
     *
     * @return string
     */
    protected function getTokenAudience(): string
    {
        /** @var string $audience */
        $audience = envValue('TOKEN_AUDIENCE', 'https://phalcon.io');

        return $audience;
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
        return (time() + envValue('TOKEN_EXPIRATION', 86400));
    }
}

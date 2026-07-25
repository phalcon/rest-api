<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Services;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Exception\TokenException;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Repositories\UsersRepository;
use Phalcon\Config\Config;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Enum;
use Phalcon\Encryption\Security\JWT\Token\Parser;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Encryption\Security\JWT\Validator;

use function implode;
use function time;

/**
 * Owns the token: issuing one for a user, and answering whether one that
 * arrived with a request is valid.
 *
 * That answer used to be spread across four components - the repository found
 * the user, the middleware verified the signature itself and reached into the
 * record for the passphrase to do it, the model checked the claims, and the
 * policy (audience, lifetime) sat in a trait mixed into both a model and an
 * HTTP middleware. Nothing could answer "is this token valid" on its own, and
 * changing token policy meant a coordinated edit across all four.
 */
class TokenService
{
    /**
     * @param UsersRepository $usersRepository
     * @param Config          $config
     */
    public function __construct(
        private readonly UsersRepository $usersRepository,
        private readonly Config $config
    ) {
    }

    /**
     * The three phases of authorization, in order:
     *
     *   1. is the user carried by the token one that we know?
     *   2. was the token signed with that user's passphrase?
     *   3. do the token's claims match the ones we expect?
     *
     * Answers the user the token belongs to, or throws saying which phase
     * rejected it.
     *
     * @param string $bearer
     *
     * @return Users
     * @throws ModelException
     * @throws TokenException
     */
    public function authenticate(string $bearer): Users
    {
        $token = $this->parse($bearer);

        $user = $this->usersRepository->getByToken($token);
        if (null === $user) {
            throw new TokenException('Invalid token (user)');
        }

        if (false === $token->verify(new Hmac(), $user->get('tokenPassword'))) {
            throw new TokenException('Invalid Token (verification)');
        }

        $errors = $token->validate($this->getValidator($token, $user));
        if (true !== empty($errors)) {
            throw new TokenException(
                'Invalid Token [' . implode('; ', $errors) . ']'
            );
        }

        return $user;
    }

    /**
     * The audience every token this application issues is stamped with, and
     * the one every token it accepts must carry.
     *
     * @return string
     */
    public function getAudience(): string
    {
        /** @var string $audience */
        $audience = $this->config->path('token.audience', 'https://phalcon.io');

        return $audience;
    }

    /**
     * The expiry time for a token issued now
     *
     * @return int
     */
    public function getExpirationTime(): int
    {
        /** @var int $expiration */
        $expiration = $this->config->path('token.expiration', 86400);

        return time() + $expiration;
    }

    /**
     * The time a token issued now is issued at
     *
     * @return int
     */
    public function getIssuedAtTime(): int
    {
        return time();
    }

    /**
     * The time drift, i.e. a token issued now is valid not before
     *
     * @return int
     */
    public function getNotBeforeTime(): int
    {
        /** @var int $notBefore */
        $notBefore = $this->config->path('token.notBefore', 0);

        return time() + $notBefore;
    }

    /**
     * Builds and signs a token for this user
     *
     * @param Users $user
     *
     * @return string
     * @throws ModelException
     */
    public function issue(Users $user): string
    {
        $builder = new Builder(new Hmac());

        return $builder
            ->setIssuer($user->get('issuer'))
            ->setAudience($this->getAudience())
            ->setId($user->get('tokenId'))
            ->setIssuedAt($this->getIssuedAtTime())
            ->setNotBefore($this->getNotBeforeTime())
            ->setExpirationTime($this->getExpirationTime())
            ->setPassphrase($user->get('tokenPassword'))
            ->getToken()
            ->getToken()
        ;
    }

    /**
     * Parses a token string into the token object
     *
     * @param string $token
     *
     * @return Token
     */
    public function parse(string $token): Token
    {
        return (new Parser())->parse($token);
    }

    /**
     * The values this record and the environment expect the incoming token to
     * hold.
     *
     * @param Token $token The token from the request - it is the one validated
     * @param Users $user
     *
     * @return Validator
     * @throws ModelException
     */
    private function getValidator(Token $token, Users $user): Validator
    {
        $validator = new Validator($token, 10);

        return $validator
            ->set(Enum::AUDIENCE, $this->getAudience())
            ->set(Enum::ISSUER, $user->get('issuer'))
            ->set(Enum::ID, $user->get('tokenId'))
        ;
    }
}

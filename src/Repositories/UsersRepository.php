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

namespace Phalcon\Api\Repositories;

use Phalcon\Api\Constants\Flags;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Services\QueryService;
use Phalcon\Encryption\Security;
use Phalcon\Encryption\Security\JWT\Token\Enum;
use Phalcon\Encryption\Security\JWT\Token\Token;

/**
 * Looks users up. The knowledge of how a user is identified - by token claims
 * or by credentials - lives here rather than in a general purpose query helper.
 */
class UsersRepository
{
    /**
     * A valid bcrypt hash of a value nobody knows. Checked against when no user
     * matches, so that a miss costs the same bcrypt round as a hit and the
     * response time stops telling an attacker which usernames exist.
     */
    private const DUMMY_HASH = '$2y$10$jG0Drp7/ZBXXPPsW1zzG6uCNpNMMwTOucqTCnX5ikNmicIJ0kqcEq';

    /**
     * @param QueryService $queryService
     * @param Security     $security
     */
    public function __construct(
        private readonly QueryService $queryService,
        private readonly Security $security
    ) {
    }

    /**
     * Gets a user from the database based on the JWT token
     *
     * @param Token $token
     *
     * @return Users|null
     */
    public function getByToken(Token $token): ?Users
    {
        $parameters = [
            'issuer'  => $token->getClaims()
                               ->get(Enum::ISSUER),
            'tokenId' => $token->getClaims()
                               ->get(Enum::ID),
            'status'  => Flags::ACTIVE,
        ];

        /**
         * getFirst() rather than [0]: ResultsetInterface declares the former,
         * and answers null on an empty set.
         *
         * @var Users|null $user
         */
        $user = $this->queryService->getRecords(Users::class, $parameters)
                                   ->getFirst()
        ;

        return $user;
    }

    /**
     * Gets a user from the database based on the username and password.
     *
     * The password is not part of the query: hashes are salted, so the same
     * password hashes differently every time and no `password = :password:`
     * comparison could ever match. The record is fetched by username and the
     * password checked against the stored hash afterwards - which also keeps
     * the plain password out of the query cache key.
     *
     * The cache is bypassed: with the password out of the query, the entry is
     * keyed on the username alone, so a cached row would keep answering with a
     * stale hash - rejecting the new password and accepting the old one for as
     * long as the entry lived.
     *
     * @param string $username
     * @param string $password
     *
     * @return Users|null
     */
    public function getByUsernameAndPassword(
        string $username,
        string $password
    ): ?Users {
        $parameters = [
            'username' => $username,
            'status'   => Flags::ACTIVE,
        ];

        $result = $this->queryService->getRecords(
            Users::class,
            $parameters,
            '',
            false
        );
        /** @var Users|null $user */
        $user = $result->getFirst();

        if (null === $user) {
            $this->security->checkHash($password, self::DUMMY_HASH);

            return null;
        }

        return true === $this->security->checkHash($password, $user->get('password'))
            ? $user
            : null;
    }
}

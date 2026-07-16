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
use Phalcon\Encryption\Security\JWT\Token\Enum;
use Phalcon\Encryption\Security\JWT\Token\Token;

/**
 * Looks users up. The knowledge of how a user is identified - by token claims
 * or by credentials - lives here rather than in a general purpose query helper.
 */
class UsersRepository
{
    /**
     * @param QueryService $queryService
     */
    public function __construct(private readonly QueryService $queryService)
    {
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

        $result = $this->queryService->getRecords(Users::class, $parameters);

        return $result[0] ?? null;
    }

    /**
     * Gets a user from the database based on the username and password
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
            'password' => $password,
            'status'   => Flags::ACTIVE,
        ];

        $result = $this->queryService->getRecords(Users::class, $parameters);

        return $result[0] ?? null;
    }
}

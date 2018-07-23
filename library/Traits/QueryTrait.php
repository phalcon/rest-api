<?php

declare(strict_types=1);

namespace Niden\Traits;

use Lcobucci\JWT\Token;
use Niden\Constants\Flags;
use Niden\Constants\JWTClaims;
use Niden\Models\Users;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Trait QueryTrait
 *
 * @package Niden\Traits
 */
trait QueryTrait
{
    /**
     * Gets a user from the database based on the JWT token
     *
     * @param Token $token
     *
     * @return Users|false
     */
    protected function getUserByToken(Token $token)
    {
        $parameters  = [
            'issuer'  => $token->getClaim(JWTClaims::CLAIM_ISSUER),
            'tokenId' => $token->getClaim(JWTClaims::CLAIM_ID),
            'status'  => Flags::ACTIVE,
        ];

        $result = $this->getRecords(Users::class, $parameters);

        return $result[0] ?? false;
    }

    /**
     * Gets a user from the database based on the username and password
     *
     * @param string $username
     * @param string $password
     *
     * @return Users|false
     */
    protected function getUserByUsernameAndPassword($username, $password)
    {
        $parameters = [
            'username' => $username,
            'password' => $password,
            'status'   => Flags::ACTIVE,
        ];

        $result = $this->getRecords(Users::class, $parameters);

        return $result[0] ?? false;
    }

    /**
     * Runs a query using the builder
     *
     * @param string $class
     * @param array  $where
     * @param string $orderBy
     *
     * @return ResultsetInterface
     */
    protected function getRecords(string $class, array $where = [], string $orderBy = '')
    {
        $builder = new Builder();
        $builder->addFrom($class);

        foreach ($where as $field => $value) {
            $builder->andWhere(
                sprintf('%s = :%s:', $field, $field),
                [$field => $value]
            );
        }

        if (true !== empty($orderBy)) {
            $builder->orderBy($orderBy);
        }

        return $builder->getQuery()->execute();
    }
}

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
 * Trait UserTrait
 *
 * @package Niden\Traits
 */
trait UserTrait
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
            'usr_issuer'      => $token->getClaim(JWTClaims::CLAIM_ISSUER),
            'usr_token_id'    => $token->getClaim(JWTClaims::CLAIM_ID),
            'usr_status_flag' => Flags::ACTIVE,
        ];

        $result = $this->getUsers($parameters);

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
            'usr_username'    => $username,
            'usr_password'    => $password,
            'usr_status_flag' => Flags::ACTIVE,
        ];

        $result = $this->getUsers($parameters);

        return $result[0] ?? false;
    }

    /**
     * @param array  $parameters
     *
     * @return ResultsetInterface
     */
    protected function getUsers(array $parameters = [])
    {
        $builder = new Builder();
        $builder->addFrom(Users::class);

        foreach ($parameters as $field => $value) {
            $builder->andWhere(
                sprintf('%s = :%s:', $field, $field),
                [$field => $value]
            );
        }

        return $builder->getQuery()->execute();
    }
}

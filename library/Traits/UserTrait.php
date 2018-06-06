<?php

namespace Niden\Traits;

use function explode;
use Niden\Exception\ModelException;
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
     * @param string $token
     *
     * @return Users|false
     */
    protected function getUserByToken(string $token)
    {
        list($pre, $mid, $post) = explode('.', $token);

        $parameters = [
            'usr_token_pre'  => $pre,
            'usr_token_mid'  => $mid,
            'usr_token_post' => $post,
        ];

        return $this->getUser($parameters);
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
            'usr_username' => $username,
            'usr_password' => $password,
        ];

        return $this->getUser($parameters);
    }

    /**
     * @param array  $parameters
     *
     * @return Users|false
     */
    protected function getUser(array $parameters = [])
    {
        $results = $this->getUsers($parameters);

        return $results[0] ?? false;
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

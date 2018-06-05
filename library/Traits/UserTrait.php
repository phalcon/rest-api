<?php

namespace Niden\Traits;

use function explode;
use Niden\Exception\ModelException;
use Niden\Models\Users;
use Phalcon\Mvc\Model\Query\Builder;

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
     * @param string $message
     *
     * @return Users
     * @throws ModelException
     */
    protected function getUserByToken(string $token, string $message = 'Record not found'): Users
    {
        list($pre, $mid, $post) = explode('.', $token);

        $parameters = [
            'usr_token_pre'  => $pre,
            'usr_token_mid'  => $mid,
            'usr_token_post' => $post,
        ];
        $results    = $this->getUsers($parameters, $message);

        return $results[0];
    }

    /**
     * Gets a user from the database based on the username and password
     *
     * @param string $username
     * @param string $password
     * @param string $message
     *
     * @return Users
     * @throws ModelException
     */
    protected function getUserByUsernameAndPassword(
        $username,
        $password,
        string $message = 'Record not found'
    ): Users {
        $parameters = [
            'usr_username'  => $username,
            'usr_password'  => $password,
        ];

        $results    = $this->getUsers($parameters, $message);

        return $results[0];
    }

    /**
     * @param array  $parameters
     * @param string $message
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     * @throws ModelException
     */
    protected function getUsers(array $parameters, string $message = 'Record not found')
    {
        $builder = new Builder();
        $builder->addFrom(Users::class);

        foreach ($parameters as $field => $value) {
            $builder->andWhere(
                sprintf('%s = :%s:', $field, $field),
                [$field => $value]
            );
        }

        $results = $builder->getQuery()->execute();

        if (0 === count($results)) {
            throw new ModelException($message);
        }

        return $results;
    }
}

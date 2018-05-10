<?php

namespace Niden\Traits;

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
        $builder = new Builder();
        $user    = $builder
            ->addFrom(Users::class)
            ->andWhere('usr_token = :usr_token:', ['usr_token' => $token])
            ->getQuery()
            ->setUniqueRow(true)
            ->execute()
        ;

        return $this->checkResult($user, $message);
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
        $buider = new Builder();
        $user   = $buider
            ->addFrom(Users::class)
            ->andWhere('usr_username = :u:', ['u' => $username])
            ->andWhere('usr_password = :p:', ['p' => $password])
            ->getQuery()
            ->setUniqueRow(true)
            ->execute();

        return $this->checkResult($user, $message);
    }

    /**
     * @param        $result
     * @param string $message
     *
     * @return Users
     * @throws ModelException
     */
    private function checkResult($result, string $message = 'Record not found'): Users
    {
        if (false === $result) {
            throw new ModelException($message);
        }

        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Niden\Traits;

use function json_encode;
use Lcobucci\JWT\Token;
use Niden\Constants\Flags;
use Niden\Constants\JWTClaims;
use Niden\Models\Users;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\ResultsetInterface;
use function sha1;

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
     * @param Libmemcached $cache
     * @param Token        $token
     *
     * @return Users|false
     */
    protected function getUserByToken(Libmemcached $cache, Token $token)
    {
        $parameters  = [
            'issuer'  => $token->getClaim(JWTClaims::CLAIM_ISSUER),
            'tokenId' => $token->getClaim(JWTClaims::CLAIM_ID),
            'status'  => Flags::ACTIVE,
        ];

        $result = $this->getRecords($cache, Users::class, $parameters);

        return $result[0] ?? false;
    }

    /**
     * Gets a user from the database based on the username and password
     *
     * @param Libmemcached $cache
     * @param string       $username
     * @param string       $password
     *
     * @return Users|false
     */
    protected function getUserByUsernameAndPassword(Libmemcached $cache, $username, $password)
    {
        $parameters = [
            'username' => $username,
            'password' => $password,
            'status'   => Flags::ACTIVE,
        ];

        $result = $this->getRecords($cache, Users::class, $parameters);

        return $result[0] ?? false;
    }

    /**
     * Runs a query using the builder
     *
     * @param Libmemcached $cache
     * @param string       $class
     * @param array        $where
     * @param string       $orderBy
     *
     * @return ResultsetInterface
     */
    protected function getRecords(
        Libmemcached $cache,
        string $class,
        array $where = [],
        string $orderBy = ''
    ): ResultsetInterface {
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

        return $this->getResults($cache, $builder, $where);
    }

    /**
     * Runs the builder query if there is no cached data
     *
     * @param Libmemcached $cache
     * @param Builder      $builder
     * @param array        $where
     *
     * @return ResultsetInterface
     */
    private function getResults(Libmemcached $cache, Builder $builder, array $where = []): ResultsetInterface
    {
        /**
         * Calculate the cache key
         */
        $phql     = $builder->getPhql();
        $params   = json_encode($where);
        $cacheKey = sha1(sprintf('%s-%s.cache', $phql, $params));
        if (true === $cache->exists($cacheKey)) {
            $data = $cache->get($cacheKey);
        } else {
            $data = $builder->getQuery()->execute();
            $cache->save($cacheKey, $data);
        }

        return $data;
    }
}

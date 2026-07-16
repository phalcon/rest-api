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

namespace Phalcon\Api\Traits;

use Phalcon\Api\Constants\Flags;
use Phalcon\Api\Models\Users;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Encryption\Security\JWT\Token\Enum;
use Phalcon\Encryption\Security\JWT\Token\Token;

use function json_encode;
use function sha1;
use function sprintf;

/**
 * Trait QueryTrait
 */
trait QueryTrait
{
    /**
     * Gets a user from the database based on the JWT token
     *
     * @param Config $config
     * @param Cache  $cache
     * @param Token  $token
     *
     * @return Users|null
     */
    protected function getUserByToken(
        Config $config,
        Cache $cache,
        Token $token
    ): ?Users {
        $parameters = [
            'issuer'  => $token->getClaims()
                               ->get(Enum::ISSUER),
            'tokenId' => $token->getClaims()
                               ->get(Enum::ID),
            'status'  => Flags::ACTIVE,
        ];

        $result = $this->getRecords($config, $cache, Users::class, $parameters);

        return $result[0] ?? null;
    }

    /**
     * Gets a user from the database based on the username and password
     *
     * @param Config $config
     * @param Cache  $cache
     * @param string $username
     * @param string $password
     *
     * @return Users|null
     */
    protected function getUserByUsernameAndPassword(
        Config $config,
        Cache $cache,
        string $username,
        string $password
    ): ?Users {
        $parameters = [
            'username' => $username,
            'password' => $password,
            'status'   => Flags::ACTIVE,
        ];

        $result = $this->getRecords($config, $cache, Users::class, $parameters);

        return $result[0] ?? null;
    }

    /**
     * Runs a query using the builder
     *
     * @param Config $config
     * @param Cache  $cache
     * @param string $class
     * @param array  $where
     * @param string $orderBy
     *
     * @return ResultsetInterface
     */
    protected function getRecords(
        Config $config,
        Cache $cache,
        string $class,
        array $where = [],
        string $orderBy = ''
    ): ResultsetInterface {
        $builder = new Builder();
        $builder->addFrom($class, 't1');

        foreach ($where as $field => $value) {
            $builder->andWhere(
                sprintf('%s = :%s:', $field, $field),
                [$field => $value]
            );
        }

        if (true !== empty($orderBy)) {
            $builder->orderBy($orderBy);
        }

        return $this->getResults($config, $cache, $builder, $where);
    }

    /**
     * Runs the builder query if there is no cached data
     *
     * @param Config  $config
     * @param Cache   $cache
     * @param Builder $builder
     * @param array   $where
     *
     * @return ResultsetInterface
     */
    private function getResults(
        Config $config,
        Cache $cache,
        Builder $builder,
        array $where = []
    ): ResultsetInterface {
        /**
         * Calculate the cache key
         */
        $phql     = $builder->getPhql();
        $params   = json_encode($where);
        $cacheKey = sha1(sprintf('%s-%s.cache', $phql, $params));
        if (
            true !== $config->path('app.devMode') &&
            true === $cache->has($cacheKey)
        ) {
            /** @var ResultsetInterface $data */
            $data = $cache->get($cacheKey);
        } else {
            $data = $builder->getQuery()
                            ->execute()
            ;
            $cache->set($cacheKey, $data);
        }

        return $data;
    }
}

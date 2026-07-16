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

use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\ResultsetInterface;

use function json_encode;
use function sha1;
use function sprintf;

/**
 * Reads records through the query builder, serving them from the cache when
 * one is warm and the application is not in dev mode.
 *
 * The config and the cache are collaborators, held once, rather than arguments
 * threaded through every call.
 */
class QueryService
{
    /**
     * @param Config $config
     * @param Cache  $cache
     */
    public function __construct(
        private readonly Config $config,
        private readonly Cache $cache
    ) {
    }

    /**
     * Runs a query using the builder
     *
     * @param string               $class
     * @param array<string, mixed> $where
     * @param string               $orderBy
     * @param bool                 $useCache Pass false for anything that must
     *                                       read the current row - see
     *                                       getResults()
     *
     * @return ResultsetInterface
     */
    public function getRecords(
        string $class,
        array $where = [],
        string $orderBy = '',
        bool $useCache = true
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

        return $this->getResults($builder, $where, $useCache);
    }

    /**
     * Runs the builder query if there is no cached data.
     *
     * The cache key is derived from the query and its bound values, so a row
     * whose other columns changed is still served from the entry keyed on the
     * columns that did not. That is fine for the read endpoints and wrong for a
     * credential check, which is why the caller can opt out.
     *
     * @param Builder              $builder
     * @param array<string, mixed> $where
     * @param bool                 $useCache
     *
     * @return ResultsetInterface
     */
    private function getResults(
        Builder $builder,
        array $where = [],
        bool $useCache = true
    ): ResultsetInterface {
        /**
         * Calculate the cache key
         */
        $phql     = $builder->getPhql();
        $params   = json_encode($where);
        $cacheKey = sha1(sprintf('%s-%s.cache', $phql, $params));

        /**
         * One decision, used for both the read and the write. A query that is
         * never read back is not written either: dev mode means no caching
         * rather than write-only caching, and an opted-out query keeps the
         * users' hashes out of the cache entirely.
         */
        $isCacheable = true === $useCache &&
            true !== $this->config->path('app.devMode');

        if (true === $isCacheable && true === $this->cache->has($cacheKey)) {
            /** @var ResultsetInterface $data */
            $data = $this->cache->get($cacheKey);
        } else {
            $data = $builder->getQuery()
                            ->execute()
            ;

            if (true === $isCacheable) {
                $this->cache->set($cacheKey, $data);
            }
        }

        return $data;
    }
}

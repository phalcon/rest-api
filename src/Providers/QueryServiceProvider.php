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

namespace Phalcon\Api\Providers;

use Phalcon\Api\Services\QueryService;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class QueryServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the query service
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared(
            'queryService',
            /**
             * Resolved lazily - `cache` is registered after this provider runs.
             */
            function () use ($container) {
                return new QueryService(
                    $container->getShared('config'),
                    $container->getShared('cache')
                );
            }
        );
    }
}

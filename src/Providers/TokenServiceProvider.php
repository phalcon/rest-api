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

use Phalcon\Api\Services\TokenService;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class TokenServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the token service
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared(
            'tokenService',
            /**
             * Resolved lazily - `usersRepository` is registered alongside this
             * provider and may not be in the container yet.
             */
            function () use ($container) {
                return new TokenService(
                    $container->getShared('usersRepository'),
                    $container->getShared('config')
                );
            }
        );
    }
}

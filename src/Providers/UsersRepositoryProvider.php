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

use Phalcon\Api\Repositories\UsersRepository;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class UsersRepositoryProvider implements ServiceProviderInterface
{
    /**
     * Registers the users repository
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared(
            'usersRepository',
            function () use ($container) {
                return new UsersRepository(
                    $container->getShared('queryService'),
                    $container->getShared('security')
                );
            }
        );
    }
}

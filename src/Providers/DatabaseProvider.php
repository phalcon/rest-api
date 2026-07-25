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

use Phalcon\Config\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        /** @var Config $config */
        $config = $container->getShared('config');

        $container->setShared(
            'db',
            function () use ($config) {
                /** @var array<string, string> $options */
                $options = $config->path('database')
                                  ->toArray()
                ;

                $connection = new Mysql($options);
                // Set everything to UTF8
                $connection->execute('SET NAMES utf8mb4', []);

                return $connection;
            }
        );
    }
}

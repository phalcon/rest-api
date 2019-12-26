<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Providers;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use function Phalcon\Api\Core\envValue;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared(
            'db',
            function () {
                $options = [
                    'host'       => envValue('DATA_API_MYSQL_HOST', 'localhost'),
                    'username'   => envValue('DATA_API_MYSQL_USER', 'nanobox'),
                    'password'   => envValue('DATA_API_MYSQL_PASS', ''),
                    'dbname'     => envValue('DATA_API_MYSQL_NAME', 'gonano'),
                ];

                $connection = new Mysql($options);
                // Set everything to UTF8
                $connection->execute('SET NAMES utf8mb4', []);

                return $connection;
            }
        );
    }
}

<?php

declare(strict_types=1);

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
    public function register(DiInterface $container)
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

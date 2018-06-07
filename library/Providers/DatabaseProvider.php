<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Config $config */
        $config = $container->getShared('config');

        $container->setShared(
            'db',
            function () use ($config) {

                $options = [
                    'host'       => $config->path('db.host', 'localhost'),
                    'username'   => $config->path('db.username', ''),
                    'password'   => $config->path('db.password', ''),
                    'dbname'     => $config->path('db.dbname', ''),
                ];

                $connection = new Mysql($options);
                // Set everything to UTF8
                $connection->execute('SET NAMES utf8mb4', []);

                return $connection;
            }
        );
    }
}

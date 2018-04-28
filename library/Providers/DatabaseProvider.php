<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Events\Manager;
use Phalcon\Registry;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Config $config */
        $config = $container->getShared('config');
        /** @var Manager $eventsManager */
        $eventsManager = $container->getShared('eventsManager');


        $container->setShared(
            'db',
            function () use ($eventsManager, $config) {

                $options = [
                    'host'       => $config->path('db.host', 'localhost'),
                    'username'   => $config->path('db.username', ''),
                    'password'   => $config->path('db.password', ''),
                    'dbname'     => $config->path('db.dbname', ''),
                ];

                $connection = new Mysql($options);
                // Set everything to UTF8
                $connection->execute('SET NAMES utf8mb4', []);
                $connection->setEventsManager($eventsManager);

                return $connection;
            }
        );
    }
}

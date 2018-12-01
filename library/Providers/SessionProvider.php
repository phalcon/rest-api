<?php

namespace Gewaer\Providers;

use function Gewaer\Core\envValue;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Memcached;
use Phalcon\Session\Adapter\Libmemcached;

class SessionProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $config = $container->getShared('config');

        $container->setShared(
            'session',
            function () use ($config) {
                $backOptions = [
                    'servers' => [
                        0 => [
                            'host' => envValue('DATA_API_MEMCACHED_HOST', '127.0.0.1'),
                            'port' => envValue('DATA_API_MEMCACHED_PORT', 11211),
                            'weight' => envValue('DATA_API_MEMCACHED_WEIGHT', 100),
                        ],
                    ],
                    'client' => [
                        Memcached::OPT_HASH => Memcached::HASH_MD5,
                        Memcached::OPT_PREFIX_KEY => 'bakasession' . $config->app->id . '-',
                    ],
                    'lifetime' => 8600,
                    'prefix' => 'bakasession' . $config->app->id . '-',
                    'persistent' => false
                ];

                $memcache = new Libmemcached($backOptions);

                $memcache->start();

                return $memcache;
            }
        );
    }
}

<?php

namespace Gewaer\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class SessionProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'session',
            function () {
                $memcache = new \Phalcon\Session\Adapter\Memcache([
                    'host' => envValue('DATA_API_MEMCACHED_HOST', '127.0.0.1'),
                    'post' => envValue('DATA_API_MEMCACHED_PORT', 11211),
                    'lifetime' => 8600, // optional (standard: 8600)
                    'prefix' => 'baka-api', // optional (standard: [empty_string]), means memcache key is my-app_31231jkfsdfdsfds3
                    'persistent' => false, // optional (standard: false)
                ]);

                $memcache->start();

                return $memcache;
            }
        );
    }
}

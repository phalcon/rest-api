<?php

namespace Niden\Providers;

use function Niden\Core\envValue;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Cache\Frontend\Data;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class CacheDataProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'cache',
            function () {
                $prefix       = 'data';
                $frontAdapter = Data::class;
                $frontOptions = [
                    'lifetime' => envValue('CACHE_LIFETIME', 86400),
                ];
                $backOptions = [
                    'servers'  => [
                        0 => [
                            'host'   => envValue('DATA_API_MEMCACHED_HOST', '127.0.0.1'),
                            'port'   => envValue('DATA_API_MEMCACHED_PORT', 11211),
                            'weight' => envValue('DATA_API_MEMCACHED_WEIGHT', 100),
                        ],
                    ],
                    'client'   => [
                        \Memcached::OPT_HASH       => \Memcached::HASH_MD5,
                        \Memcached::OPT_PREFIX_KEY => 'api-',
                    ],
                    'lifetime' => 3600,
                    'prefix'   => $prefix . '-',
                ];

                return new Libmemcached(new $frontAdapter($frontOptions), $backOptions);
            }
        );
    }
}

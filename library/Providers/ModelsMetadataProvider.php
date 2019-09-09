<?php

declare(strict_types=1);

namespace Niden\Providers;

use function Niden\Core\envValue;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\MetaData\Libmemcached;

class ModelsMetadataProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'modelsMetadata',
            function () {
                $prefix      = 'metadata';
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

                return new Libmemcached($backOptions);
            }
        );
    }
}

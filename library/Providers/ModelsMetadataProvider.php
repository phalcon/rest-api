<?php

declare(strict_types=1);

namespace Phalcon\Api\Providers;

use Phalcon\Cache\AdapterFactory;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Model\MetaData\Libmemcached;
use Phalcon\Storage\SerializerFactory;
use function Niden\Core\envValue;

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
                $backOptions = [
                    'servers'  => [
                        0 => [
                            'host'   => envValue('DATA_API_MEMCACHED_HOST', '127.0.0.1'),
                            'port'   => envValue('DATA_API_MEMCACHED_PORT', 11211),
                            'weight' => envValue('DATA_API_MEMCACHED_WEIGHT', 100),
                        ],
                    ],
                    'client'   => [
                        \Memcached::OPT_PREFIX_KEY => 'api-',
                    ],
                    'lifetime' => 3600,
                    'prefix'   => 'metadata-',
                ];

                $serializer = new SerializerFactory();
                $adapterFactor = new AdapterFactory($serializer);

                return new Libmemcached($adapterFactor, $backOptions);
            }
        );
    }
}

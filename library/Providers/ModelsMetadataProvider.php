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

use Phalcon\Cache\AdapterFactory;
use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Model\MetaData\Memory;
use Phalcon\Mvc\Model\MetaData\Redis;
use Phalcon\Storage\SerializerFactory;

class ModelsMetadataProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        /** @var Config $config */
        $config = $container->getShared('config');

        $container->setShared(
            'modelsMetadata',
            function () use ($config) {
                $metadata = $config->get('metadata');
                $devMode  = $config->path('app.devMode');
                $key      = (true === $devMode) ? 'dev' : 'prod';
                $options  = $metadata->get($key, [])
                                     ->toArray()
                ;
                $adapter  = $options['adapter'] ?? Redis::class;

                if ($adapter === Memory::class) {
                    return new $adapter($options);
                } else {
                    $serializer     = new SerializerFactory();
                    $adapterFactory = new AdapterFactory($serializer);

                    return new $adapter($adapterFactory, $options);
                }
            }
        );
    }
}

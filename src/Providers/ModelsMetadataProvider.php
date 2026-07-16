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
                $entry    = $metadata->get($key, [])
                                     ->toArray()
                ;

                /**
                 * The entry is ['adapter' => ..., 'options' => [...]]; the
                 * adapter wants the inner array. Handing it the outer one costs
                 * it the host and sends it to 127.0.0.1.
                 */
                $adapter = $entry['adapter'] ?? Redis::class;
                $options = $entry['options'] ?? [];

                if ($adapter === Memory::class) {
                    return new $adapter($options);
                }

                $serializer     = new SerializerFactory();
                $adapterFactory = new AdapterFactory($serializer);

                return new $adapter($adapterFactory, $options);
            }
        );
    }
}

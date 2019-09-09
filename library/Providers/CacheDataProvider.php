<?php

namespace Phalcon\Api\Providers;

use Phalcon\Cache;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Storage\SerializerFactory;
use function Niden\Core\appPath;

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
                /** @var array $backOptions */
                $options = include appPath('cli/config/cache.php');

                $serializer = new SerializerFactory();
                $adapter = new Cache\Adapter\Libmemcached($serializer, $options);

                return new Cache($adapter);
            }
        );
    }
}

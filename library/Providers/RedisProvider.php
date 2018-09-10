<?php

namespace Baka\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Redis;
use function Niden\Core\envValue;

class RedisProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $config = $container->getShared('config');

        $container->setShared(
            'redis',
            function () use ($config) {
                //Connect to redis
                //for now redis normal
                $redis = new Redis();
                $redis->connect(envValue('REDIS_HOST', '127.0.0.1'), envValue('REDIS_PORT', '6379'));
                $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                return $redis;
            }
        );
    }
}

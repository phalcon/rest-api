<?php

namespace Gewaer\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Pusher\Pusher;

class PusherProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $config = $container->getShared('config');

        $container->setShared(
            'pusher',
            function () use ($config) {
                return new Pusher($config->pusher->key, $config->pusher->secret, $config->pusher->id, ['cluster' => $config->pusher->cluster, 'useTLS' => true]);
            }
        );
    }
}

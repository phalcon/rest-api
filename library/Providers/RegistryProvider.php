<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Registry;

use function microtime;

class RegistryProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Config $config */
        $config  = $container->getShared('config');
        $devMode = $config->path('app.devMode', false);

        $container->setShared(
            'registry',
            function () use ($devMode) {
                $registry = new Registry();
                $registry->offsetSet('devMode', $devMode);
                $registry->offsetSet('execution', microtime(true));
                $registry->offsetSet('memory', 0);
                $registry->offsetSet('queries', 0);

                return $registry;
            }
        );
    }
}

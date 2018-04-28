<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use function is_array;

class ModelsCacheProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Config $config */
        $config = $container->getShared('config');
        $container->setShared(
            'modelsCache',
            function () use ($config) {
                $frontAdapter = $config->path('cache.data.front.adapter', 'Data');
                $frontOptions = $config->path('cache.data.front.options', []);
                $backAdapter  = $config->path('cache.data.back.adapter', 'File');
                $backOptions  = $config->path('cache.data.back.options', []);

                $frontAdapter = sprintf(
                    '\Phalcon\Cache\Frontend\%s',
                    ucfirst($frontAdapter)
                );
                $backAdapter  = sprintf(
                    '\Phalcon\Cache\Backend\%s',
                    ucfirst($backAdapter)
                );

                $frontOptions = (true === is_array($frontOptions)) ?
                    $frontOptions :
                    $frontOptions->toArray();
                $backOptions  = (true === is_array($backOptions)) ?
                    $backOptions :
                    $backOptions->toArray();

                return new $backAdapter(
                    new $frontAdapter($frontOptions),
                    $backOptions
                );
            }
        );
    }
}

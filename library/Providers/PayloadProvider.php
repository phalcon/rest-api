<?php

namespace Niden\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Niden\Http\Payload;

class PayloadProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'payload',
            function () {
                return new Payload();
            }
        );
    }
}

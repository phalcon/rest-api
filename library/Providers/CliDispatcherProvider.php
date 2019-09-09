<?php

declare(strict_types=1);

namespace Phalcon\Api\Providers;

use Phalcon\Cli\Dispatcher;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class CliDispatcherProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'dispatcher',
            function () {
                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace('Niden\Cli\Tasks');

                return $dispatcher;
            }
        );
    }
}

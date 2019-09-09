<?php

declare(strict_types=1);

namespace Niden\Providers;

use Phalcon\Cli\Dispatcher;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

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

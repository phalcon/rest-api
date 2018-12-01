<?php

declare(strict_types=1);

namespace Gewaer\Providers;

use Gewaer\Http\Request;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class RequestProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared('request', new Request());
    }
}

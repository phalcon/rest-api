<?php

declare(strict_types=1);

namespace Phalcon\Api\Providers;

use Phalcon\Api\Http\Request;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

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

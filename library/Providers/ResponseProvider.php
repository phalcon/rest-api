<?php

declare(strict_types=1);

namespace Niden\Providers;

use Niden\Http\Response;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class ResponseProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared('response', new Response());
    }
}

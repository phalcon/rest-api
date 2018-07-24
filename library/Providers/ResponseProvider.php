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
        $response = new Response();

        /**
         * Assume success. We will work with the edge cases in the code
         */
        $response->setStatusCode(200);

        $container->setShared('response', $response);
    }
}

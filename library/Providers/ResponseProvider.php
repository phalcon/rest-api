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
        $container->setShared(
            'response',
            function () {
                $response = new Response();

                /**
                 * Assume success. We will work with the edge cases in the code
                 */
                $response
                    ->setStatusCode(200)
                    ->setContentType('application/vnd.api+json', 'UTF-8')
                ;

                return $response;
            }
        );
    }
}

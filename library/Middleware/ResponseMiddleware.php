<?php

namespace Niden\Middleware;

use Niden\Http\Response;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class PayloadMiddleware
 *
 * @package Niden\Middleware
 */
class ResponseMiddleware implements MiddlewareInterface
{
    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return \Phalcon\Http\Response
     */
    public function call(Micro $api)
    {
        $contents = $api->getReturnedValue();
        /** @var Response $response */
        $response = $api->response;

        return $response
                ->setPayloadContent($contents)
                ->send()
            ;
    }
}

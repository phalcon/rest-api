<?php

namespace Niden\Middleware;

use Niden\Http\Response;
use Phalcon\Events\Event;
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
        return $api
                ->response
                ->setPayloadContent($contents)
                ->send()
            ;
    }
}

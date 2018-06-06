<?php

namespace Niden\Middleware;

use Niden\Http\Response;
use Niden\Traits\ResponseTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class ResponseMiddleware
 *
 * @package Niden\Middleware
 *
 * @property Response $response
 */
class ResponseMiddleware implements MiddlewareInterface
{
    use ResponseTrait;

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     */
    public function call(Micro $api)
    {
        $this->process($api);

        return true;
    }
}

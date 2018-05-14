<?php

namespace Niden\Middleware;

use Niden\TokenParser;
use Niden\Http\Response;
use Niden\Traits\UserTrait;
use Phalcon\Http\Request;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class TokenMiddleware
 *
 * @package Niden\Middleware
 *
 * @property Response $response
 */
class TokenMiddleware implements MiddlewareInterface
{
    use UserTrait;

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     */
    public function call(Micro $api)
    {
        /** @var Request $request */
        $request  = $api->getService('request');
        /** @var Response $response */
        $response = $api->getService('response');

        return (new TokenParser($request, $response))->call();
    }
}

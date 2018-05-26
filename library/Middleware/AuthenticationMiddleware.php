<?php

namespace Niden\Middleware;

use Niden\Exception\Exception;
use Niden\Http\Request;
use Niden\Traits\UserTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    use UserTrait;

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     * @throws Exception
     */
    public function call(Micro $api)
    {
        /** @var Request $request */
        $request  = $api->getService('request');

        $uri   = $request->getURI();
        $token = $request->getBearerTokenFromHeader();

        if ('/' !== $uri && '/login' !== $uri && true === empty($token)) {
            throw new Exception('Invalid Token');
        }

        return true;
    }
}

// eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoiYWFhYWFhIn0.eyJpc3MiOiJodHRwczpcL1wvcGhhbGNvbnBocC5jb20iLCJhdWQiOiJodHRwczpcL1wvbmlkZW4ubmV0IiwianRpIjoiYWFhYWFhIiwiaWF0IjoxNTI3MjgyMzYyLCJuYmYiOjE1MjcyODI0MjIsImV4cCI6MTUyNzI4NTk2MiwidWlkIjoxfQ
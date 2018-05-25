<?php

namespace Niden\Middleware;

use Niden\Exception\Exception;
use Niden\Token\Parse;
use Niden\Http\Response;
use Niden\Traits\UserTrait;
use Niden\Http\Request;
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
        try {
            /** @var Request $request */
            $request  = $api->getService('request');
            /** @var Response $response */
            $response = $api->getService('response');

            return (new Parse($request, $response))->call();
        } catch (Exception $ex) {
            $this->response->sendError('Auth', $ex->getMessage());

            return false;
        }
    }
}

// eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoiYWFhYWFhIn0.eyJpc3MiOiJodHRwczpcL1wvcGhhbGNvbnBocC5jb20iLCJhdWQiOiJodHRwczpcL1wvbmlkZW4ubmV0IiwianRpIjoiYWFhYWFhIiwiaWF0IjoxNTI3MjgyMzYyLCJuYmYiOjE1MjcyODI0MjIsImV4cCI6MTUyNzI4NTk2MiwidWlkIjoxfQ
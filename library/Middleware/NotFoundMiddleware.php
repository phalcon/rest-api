<?php

namespace Niden\Middleware;

use Niden\Http\Response;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\User\Plugin;

/**
 * Class NotFoundMiddleware
 *
 * @package Niden\Middleware
 *
 * @property Response $response
 */
class NotFoundMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeNotFound()
    {
        $this->response->sendError('', '404 Not Found');

        return false;
    }

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     */
    public function call(Micro $api)
    {
        return true;
    }
}

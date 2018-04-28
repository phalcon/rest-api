<?php

namespace Niden\Middleware;

use function get_class;
use Niden\Http\Response;
use Phalcon\Di;
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
        $this
            ->response
            ->setPayloadStatusError()
            ->setErrorDetail('404 Not Found')
            ->setPayloadContent()
            ->send();
        ;

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

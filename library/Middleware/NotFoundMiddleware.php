<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Http\Response;
use Niden\Traits\ResponseTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\User\Plugin;

/**
 * Class NotFoundMiddleware
 *
 * @package Niden\Middleware
 *
 * @property Micro    $application
 * @property Response $response
 */
class NotFoundMiddleware extends Plugin implements MiddlewareInterface
{
    use ResponseTrait;

    /**
     * Checks if the resource was found
     */
    public function beforeNotFound()
    {
        $this->halt($this->application, 404, 'Not Found');

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

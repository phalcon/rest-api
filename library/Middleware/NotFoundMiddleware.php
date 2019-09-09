<?php

declare(strict_types=1);

namespace Phalcon\Api\Middleware;

use Phalcon\Api\Http\Response;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Plugin;

/**
 * Class NotFoundMiddleware
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
        $this->halt(
            $this->application,
            $this->response::NOT_FOUND,
            $this->response->getHttpCodeDescription($this->response::NOT_FOUND)
        );

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

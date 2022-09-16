<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Middleware;

use Phalcon\Api\Http\Response;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class NotFoundMiddleware
 *
 * @property Micro    $application
 * @property Response $response
 */
class NotFoundMiddleware extends Injectable implements MiddlewareInterface
{
    use ResponseTrait;

    /**
     * Checks if the resource was found
     *
     * @return bool
     */
    public function beforeNotFound(): bool
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
    public function call(Micro $api): bool
    {
        return true;
    }
}

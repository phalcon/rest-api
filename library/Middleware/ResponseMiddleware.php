<?php

declare(strict_types=1);

namespace Phalcon\Api\Middleware;

use Phalcon\Api\Http\Response;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class ResponseMiddleware
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
        /** @var Response $response */
        $response = $api->getService('response');
        $response->send();

        return true;
    }
}

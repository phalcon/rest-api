<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    use ResponseTrait;
    use QueryTrait;

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

        if (true !== $request->isLoginPage() &&
            true === $request->isEmptyBearerToken()) {
            $this->halt(
                $api,
                $response::OK,
                'Invalid Token'
            );

            return false;
        }

        return true;
    }
}

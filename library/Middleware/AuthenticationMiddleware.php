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
        $request = $api->getService('request');

        if (true === $request->isPost() &&
            true !== $request->isLoginPage() &&
            true === $request->isEmptyBearerToken()) {
            throw new Exception('Invalid Token');
        }

        return true;
    }
}

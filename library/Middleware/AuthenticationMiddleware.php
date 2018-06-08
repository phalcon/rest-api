<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Http\Request;
use Niden\Traits\ResponseTrait;
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
    use ResponseTrait;
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
        /** @var Request $request */
        $request = $api->getService('request');

        if (true !== $request->isLoginPage() &&
            true === $request->isEmptyBearerToken()) {
            $this->halt($api, 'Invalid Token');

            return false;
        }

        return true;
    }
}

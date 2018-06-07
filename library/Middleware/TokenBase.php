<?php

namespace Niden\Middleware;

use Niden\Http\Request;
use Niden\Traits\ResponseTrait;
use Niden\Traits\TokenTrait;
use Niden\Traits\UserTrait;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
abstract class TokenBase implements MiddlewareInterface
{
    use ResponseTrait;
    use TokenTrait;
    use UserTrait;

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isValidCheck(Request $request): bool
    {
        return (
            true !== $request->isLoginPage() &&
            true !== $request->isEmptyBearerToken()
        );
    }
}

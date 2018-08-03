<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Http\Request;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Niden\Traits\TokenTrait;
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
    use QueryTrait;

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

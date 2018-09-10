<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Exception\ModelException;
use Phalcon\Mvc\Micro;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
class TokenVerificationMiddleware extends TokenBase
{
    /**
     * @param Micro $api
     *
     * @return bool
     * @throws ModelException
     */
    public function call(Micro $api)
    {
        return true;
    }
}

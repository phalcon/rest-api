<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Phalcon\Mvc\Micro;

/**
 * Class TokenUserMiddleware
 *
 * @package Niden\Middleware
 */
class TokenUserMiddleware extends TokenBase
{
    /**
     * @param Micro $api
     *
     * @return bool
     */
    public function call(Micro $api)
    {
        return true;
    }
}

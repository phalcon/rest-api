<?php

declare(strict_types=1);

namespace Niden\Http;

use function str_replace;
use Phalcon\Http\Request as PhRequest;

class Request extends PhRequest
{
    /**
     * @return string
     */
    public function getBearerTokenFromHeader(): string
    {
        return str_replace('Bearer ', '', $this->getHeader('Authorization'));
    }

    /**
     * @return bool
     */
    public function isEmptyBearerToken(): bool
    {
        return true === empty($this->getBearerTokenFromHeader());
    }

    /**
     * @return bool
     */
    public function isLoginPage(): bool
    {
        return ('/login' === $this->getURI());
    }
}

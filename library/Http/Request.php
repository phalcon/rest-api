<?php

namespace Niden\Http;

use Phalcon\Http\Request as PhRequest;
use function str_replace;

class Request extends PhRequest
{
    /**
     * @return string
     */
    public function getBearerTokenFromHeader(): string
    {
        return str_replace('Bearer ', '', $this->getHeader('Authorization'));
    }
}

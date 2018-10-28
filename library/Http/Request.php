<?php

declare(strict_types=1);

namespace Gewaer\Http;

use Phalcon\Http\Request as PhRequest;

class Request extends PhRequest
{
    /**
     * @return bool
     */
    public function isLoginPage() : bool
    {
        return ('/login' === $this->getURI());
    }
}

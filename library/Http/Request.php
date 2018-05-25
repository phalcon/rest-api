<?php

namespace Niden\Http;

use Phalcon\Http\Request as PhRequest;

class Request extends PhRequest
{
    /**
     * @return string
     */
    public function getBearerTokenFromHeader()
    {
        $header = $this->getHeader('Authorization');
        $bearer = sscanf($header, 'Bearer %s');

        return $bearer[0] ?? '';
    }
}

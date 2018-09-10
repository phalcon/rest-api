<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Exception\ModelException;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class TokenValidationMiddleware
 *
 * @package Niden\Middleware
 */
class TokenValidationMiddleware implements MiddlewareInterface
{
    /**
     * @param Micro $api
     *
     * @return bool
     * @throws ModelException
     */
    public function call(Micro $api)
    {
        $config = $api->getService('config');

        $auth = $api->getService('auth');
        // to get the payload
        $data = $auth->data();

        if (!empty($data) && $data['iat'] <= strtotime('-10 seconds')) {
            // return false to invalidate the authentication
            //throw new Exception("Old Request");
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Gewaer\Exception\ModelException;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Http\Request;
use Exception;

/**
 * Class TokenValidationMiddleware
 *
 * @package Gewaer\Middleware
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

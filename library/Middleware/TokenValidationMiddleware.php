<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Gewaer\Exception\ModelException;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Baka\Auth\Models\Sessions;
use Gewaer\Models\Users;
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

        $api->getDI()->setShared(
            'userData',
            function () use ($config, $data) {
                $session = new Sessions();
                $request = new Request();

                if (!empty($data) && !empty($data['sessionId'])) {
                    //user
                    if (!$user = Users::getByEmail($data['email'])) {
                        throw new Exception('User not found');
                    }

                    return $session->check($user, $data['sessionId'], $request->getClientAddress(), 1);
                } else {
                    throw new Exception('User not found');
                }
            }
        );

        if (!empty($data) && $data['iat'] <= strtotime('-10 seconds')) {
            // return false to invalidate the authentication
            //throw new Exception("Old Request");
        }

        return true;
    }
}

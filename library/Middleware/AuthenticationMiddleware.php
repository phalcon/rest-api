<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Baka\Auth\Models\Sessions;
use Gewaer\Models\Users;
use Phalcon\Http\Request;
use Gewaer\Exception\UnauthorizedHttpException;
use Gewaer\Constants\Flags;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * Call me
     *
     * @param Micro $api
     * @todo need to check section for auth here
     * @return bool
     */
    public function call(Micro $api)
    {
        $auth = $api->getService('auth');
        $config = $api->getService('config');

        // to get the payload
        $data = $auth->data();

        $api->getDI()->setShared(
            'userData',
            function () use ($config, $data) {
                $session = new Sessions();
                $request = new Request();

                //all is empty and is dev, ok take use the first user
                if (empty($data) && empty($data['sessionId']) && strtolower($config->app->env) == Flags::DEVELOPMENT) {
                    return Users::findFirst(1);
                }

                if (!empty($data) && !empty($data['sessionId'])) {
                    //user
                    if (!$user = Users::getByEmail($data['email'])) {
                        throw new UnauthorizedHttpException('User not found');
                    }

                    return $session->check($user, $data['sessionId'], $request->getClientAddress(), 1);
                } else {
                    throw new UnauthorizedHttpException('User not found');
                }
            }
        );

        return true;
    }
}

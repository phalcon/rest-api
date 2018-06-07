<?php

namespace Niden\Middleware;

use Niden\Http\Request;
use Niden\Models\Users;
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
        /** @var Request $request */
        $request = $api->getService('request');
        if (true === $this->isValidCheck($request)) {
            /**
             * This is where we will find if the user exists based on
             * the token passed using Bearer Authentication
             */
            $token = $this->getToken($request->getBearerTokenFromHeader());

            /** @var Users|false $user */
            $user = $this->getUserByToken($token);
            if (false === $user) {
                $this->halt($api, 'Invalid Token');

                return false;
            }
        }

        return true;
    }
}

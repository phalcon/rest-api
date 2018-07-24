<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Models\Users;
use Phalcon\Mvc\Micro;

/**
 * Class TokenValidationMiddleware
 *
 * @package Niden\Middleware
 */
class TokenValidationMiddleware extends TokenBase
{
    /**
     * @param Micro $api
     *
     * @return bool
     * @throws ModelException
     */
    public function call(Micro $api)
    {
        /** @var Request $request */
        $request = $api->getService('request');
        if (true === $this->isValidCheck($request)) {
            /**
             * This is where we will validate the token that was sent to us
             * using Bearer Authentication
             *
             * Find the user attached to this token
             */
            $token = $this->getToken($request->getBearerTokenFromHeader());

            /** @var Users $user */
            $user = $this->getUserByToken($token);
            if (false === $token->validate($user->getValidationData())) {
                $this->halt($api, 200, 'Invalid Token');

                return false;
            }
        }

        return true;
    }
}

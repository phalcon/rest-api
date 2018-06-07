<?php

namespace Niden\Middleware;

use Lcobucci\JWT\Parser;
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
            $dbToken = $request->getBearerTokenFromHeader();
            $token   = (new Parser())->parse($dbToken);

            /** @var Users $user */
            $user = $this->getUserByToken($token);
            if (false === $token->validate($user->getValidationData())) {
                $this->halt($api, 'Invalid Token');

                return false;
            }
        }

        return true;
    }
}

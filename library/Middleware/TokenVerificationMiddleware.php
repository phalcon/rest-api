<?php

namespace Niden\Middleware;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Models\Users;
use Phalcon\Mvc\Micro;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
class TokenVerificationMiddleware extends TokenBase
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
            $token  = $this->getToken($request->getBearerTokenFromHeader());
            $signer = new Sha512();

            /** @var Users $user */
            $user = $this->getUserByToken($token);
            if (false === $token->verify($signer, $user->get('usr_token_password'))) {
                $this->halt($api, 'Invalid Token');
            }
        }

        return true;
    }
}

<?php

namespace Niden\Middleware;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Niden\Exception\Exception;
use Niden\Http\Request;
use Niden\Models\Users;
use Niden\Traits\UserTrait;
use Phalcon\Filter;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    use UserTrait;

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     * @throws Exception
     */
    public function call(Micro $api)
    {
        /** @var Request $request */
        $request = $api->getService('request');
        $uri     = $request->getURI();
        $token   = $request->getBearerTokenFromHeader();

        if (true === $request->isPost() && '/login' !== $uri && true !== empty($token)) {
            /**
             * This is where we will validate the token that was sent to us
             * using Bearer Authentication
             */
            $user = $this->getUserByToken($token, 'Invalid Token');

            $tokenObject    = (new Parser())->parse($token);
            $validationData = $this->getValidation($user);
            $valid          = $tokenObject->validate($validationData);
            if (false === $valid) {
                throw new Exception('Invalid Token');
            }
        }

        return true;
    }

    /**
     * @param Users $user
     *
     * @return ValidationData
     * @throws \Niden\Exception\ModelException
     */
    private function getValidation(Users $user)
    {
        $validationData = new ValidationData();
        $validationData->setIssuer('https://phalconphp.com');
        $validationData->setAudience($user->get('usr_domain_name'));
        $validationData->setId($user->get('usr_token_id'));

        return $validationData;
    }
}

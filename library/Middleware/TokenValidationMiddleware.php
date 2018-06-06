<?php

namespace Niden\Middleware;

use function time;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\ValidationData;
use Niden\Exception\Exception;
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
     */
    public function call(Micro $api)
    {
        /** @var Request $request */
        $request = $api->getService('request');
        try {
            if (true === $this->isValidCheck($request)) {
                /**
                 * This is where we will validate the token that was sent to us
                 * using Bearer Authentication
                 *
                 * Find the user attached to this token
                 */
                $token = $request->getBearerTokenFromHeader();
                /** @var Users $user */
                $user = $this->getTokenUser($token);

                /**
                 * Parse the token and validate it
                 */
                $this->checkToken($token, $user);
            }

            return true;
        } catch (Exception $ex) {
            $this->halt($api, $ex->getMessage());

            return false;
        }
    }

    /**
     * @param string $token
     * @param Users  $user
     *
     * @throws Exception
     * @throws ModelException
     */
    private function checkToken(string $token, Users $user)
    {
        $token    = (new Parser())->parse($token);
        $data     = $this->getValidation($user);
        $valid    = $token->validate($data);

        if (false === $valid) {
            throw new Exception('Invalid Token');
        }
    }

    /**
     * @param Users $user
     *
     * @return ValidationData
     * @throws ModelException
     */
    private function getValidation(Users $user)
    {
        $validationData = new ValidationData();
        $validationData->setIssuer($user->get('usr_domain_name'));
        $validationData->setAudience('https://phalconphp.com');
        $validationData->setId($user->get('usr_token_id'));
        $validationData->setCurrentTime(time() + 10);

        return $validationData;
    }
}

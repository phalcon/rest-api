<?php

namespace Niden\Middleware;

use function time;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Niden\Exception\Exception;
use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Models\Users;
use Niden\Traits\ResponseTrait;
use Niden\Traits\UserTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    use ResponseTrait;
    use UserTrait;

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

        if (true === $request->isPost() &&
            true !== $request->isLoginPage() &&
            true !== $request->isEmptyBearerToken()) {
            /**
             * This is where we will validate the token that was sent to us
             * using Bearer Authentication
             */
            /**
             * Find the user attached to this token
             */
            $dbToken = $request->getBearerTokenFromHeader();
            /** @var Users|false $user */
            $user  = $this->getUserByToken($dbToken);
            if (false === $user) {
                $this->halt($api, 'Invalid Token');
                return false;
            }

            /**
             * Parse the token and verify signature
             */
            $token = (new Parser())->parse($dbToken);
            if (false === $this->checkTokenSignature($token, $user)) {
                $this->halt($api, 'Invalid Token');
                return false;
            }

            /**
             * Validate the token
             */
            if (false === $this->checkTokenValidity($token, $user)) {
                $this->halt($api, 'Invalid Token');
                return false;
            }
        }

        return true;
    }

    /**
     * @param Token $token
     * @param Users $user
     *
     * @return bool
     * @throws ModelException
     */
    private function checkTokenSignature(Token $token, Users $user): bool
    {
        $signer = new Sha512();

        return $token->verify($signer, $user->get('usr_token_password'));
    }

    /**
     * @param Token $token
     * @param Users $user
     *
     * @return bool
     * @throws ModelException
     */
    private function checkTokenValidity(Token $token, Users $user): bool
    {
        $data = $this->getValidation($user);

        return $token->validate($data);
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

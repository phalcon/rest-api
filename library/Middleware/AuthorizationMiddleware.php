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
            $token  = (new Parser())->parse($dbToken);
            $signer = new Sha512();
            $valid  = $token->verify($signer, $user->get('usr_token_password'));

            if (false === $valid) {
                $this->halt($api, 'Invalid Token');
                return false;
            }

            /**
             * Validate the token
             */
            $data  = $this->getValidation($user);
            $valid = $token->validate($data);

            if (false === $valid) {
                $this->halt($api, 'Invalid Token');
                return false;
            }
//            $this->checkAlteredToken($parsedToken, $user);
//            $this->checkValidToken($parsedToken, $user);
        }

        return true;
//        } catch (Exception $ex) {
//            $this->halt($api, $ex->getMessage());
//
//            return false;
//        }
    }

    /**
     * @param Token $token
     * @param Users $user
     *
     * @throws Exception
     * @throws ModelException
     */
    private function checkAlteredToken(Token $token, Users $user)
    {
        $signer = new Sha512();
        $valid  = $token->verify($signer, $user->get('usr_token_password'));

        if (false === $valid) {
            throw new Exception('Invalid Token');
        }
    }

    /**
     * @param Token $token
     * @param Users $user
     *
     * @throws Exception
     * @throws ModelException
     */
    private function checkValidToken(Token $token, Users $user)
    {
        $data  = $this->getValidation($user);
        $valid = $token->validate($data);

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

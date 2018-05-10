<?php

namespace Niden\Middleware;

use Niden\Exception\Exception;
use function sscanf;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\UserTrait;
use Phalcon\Http\Request;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * Class TokenMiddleware
 *
 * @package Niden\Middleware
 *
 * @property Response $response
 */
class TokenMiddleware implements MiddlewareInterface
{
    use UserTrait;

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     */
    public function call(Micro $api)
    {
        /** @var Request $request */
        $request = $api->getService('request');
        $uri     = $request->getURI();
        if ('/login' === $uri) {
            try {
                /**
                 * This is where we will validate the token that was sent to us
                 * using Bearer Authentication
                 */
                $token  = $this->getTokenFromHeader($request);
                $user   = $this->getUserByToken($token, 'Invalid Token');

                $tokenObject    = $this->parseToken($user, $token);
                $validationData = $this->getValidationData($user);

                $valid = $tokenObject->validate($validationData);

                if (false === $valid) {
                    throw new Exception('Invalid Token');
                }
            } catch (Exception $ex) {
                /** @var Response $response */
                $response = $api->getService('response');
                $response
                    ->setError('Auth', $ex->getMessage())
                    ->setPayloadContent()
                    ->send()
                ;

                return false;
            }
        }

        return true;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getTokenFromHeader(Request $request)
    {
        $header = $request->getHeader('Authorization');
        $bearer = sscanf($header, 'Bearer %s');

        return $bearer[0] ?? '';
    }

    /**
     * @param Users $user
     *
     * @return ValidationData
     * @throws \Niden\Exception\ModelException
     */
    private function getValidationData(Users $user)
    {
        $validationData = new ValidationData();
        $validationData->setIssuer('phalconphp.com');
        $validationData->setAudience($user->get('usr_domain_name'));
        $validationData->setId($user->get('usr_token_id'));

        return $validationData;
    }

    /**
     * @param Users  $user
     * @param string $token
     *
     * @return \Lcobucci\JWT\Token
     */
    private function parseToken(Users $user, string $token)
    {
        return (new Parser())->parse($token);
    }
}

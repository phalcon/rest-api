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
                $header = $request->getHeader('Authorization');
                $bearer = sscanf($header, 'Bearer %s');
                $token  = $bearer[0] ?? '';
                $user   = $this->getUserByToken($token, 'Invalid Token');

                $audience    = $user->get('usr_domain_name');
                $issuer      = 'phalconphp.com';
                $token       = $user->get('usr_token');
                $tokenObject = (new Parser())->parse($token);
                $tokenId     = $user->get('usr_token_id');

                $validationData = new ValidationData();
                $validationData->setIssuer($issuer);
                $validationData->setAudience($audience);
                $validationData->setId($tokenId);

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
            }
        }

        return true;
    }
}

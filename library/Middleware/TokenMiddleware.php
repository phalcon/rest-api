<?php

namespace Niden\Middleware;

use Lcobucci\JWT\Builder;
use Niden\Exception\Exception;
use Niden\Token\Parse;
use Niden\Http\Response;
use Niden\Traits\UserTrait;
use Phalcon\Http\Request;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

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
        try {

            $token = (new Builder())->setIssuer('https://phalconphp.com')  // Configures the issuer (iss claim)
                                    ->setAudience('https://niden.net')   // Configures the audience (aud claim)
                                    ->setId('aaaaaa', true)   // Configures the id (jti claim), replicating as a header item
                                    ->setIssuedAt(time())                         // Configures the time that the token was issue (iat claim)
                                    ->setNotBefore(time() + 60)         // Configures the time that the token can be used (nbf claim)
                                    ->setExpiration(time() + 3600)      // Configures the expiration time of the token (exp claim)
                                    ->set('uid', 1) // Configures a new claim, called "uid"
                                    ->getToken();
            $x = $token->getPayload();
            $token = (string) $token;

            /** @var Request $request */
            $request  = $api->getService('request');
            /** @var Response $response */
            $response = $api->getService('response');

            return (new Parse($request, $response))->call();
        } catch (Exception $ex) {
            $this->response->sendError('Auth', $ex->getMessage());

            return false;
        }
    }
}

// eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoiYWFhYWFhIn0.eyJpc3MiOiJodHRwczpcL1wvcGhhbGNvbnBocC5jb20iLCJhdWQiOiJodHRwczpcL1wvbmlkZW4ubmV0IiwianRpIjoiYWFhYWFhIiwiaWF0IjoxNTI3MjgyMzYyLCJuYmYiOjE1MjcyODI0MjIsImV4cCI6MTUyNzI4NTk2MiwidWlkIjoxfQ
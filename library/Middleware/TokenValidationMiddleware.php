<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Models\Users;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Config;
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
        /** @var Libmemcached $cache */
        $cache    = $api->getService('cache');
        /** @var Config $config */
        $config   = $api->getService('config');
        /** @var Request $request */
        $request  = $api->getService('request');
        /** @var Response $response */
        $response = $api->getService('response');
        if (true === $this->isValidCheck($request)) {
            /**
             * This is where we will validate the token that was sent to us
             * using Bearer Authentication
             *
             * Find the user attached to this token
             */
            $token = $this->getToken($request->getBearerTokenFromHeader());

            /** @var Users $user */
            $user = $this->getUserByToken($config, $cache, $token);
            if (false === $token->validate($user->getValidationData())) {
                $this->halt(
                    $api,
                    $response::OK,
                    'Invalid Token'
                );

                return false;
            }
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace Niden\Middleware;

use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Models\Users;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Config;
use Phalcon\Mvc\Micro;

/**
 * Class TokenUserMiddleware
 *
 * @package Niden\Middleware
 */
class TokenUserMiddleware extends TokenBase
{
    /**
     * @param Micro $api
     *
     * @return bool
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
             * This is where we will find if the user exists based on
             * the token passed using Bearer Authentication
             */
            $token = $this->getToken($request->getBearerTokenFromHeader());

            /** @var Users|false $user */
            $user = $this->getUserByToken($config, $cache, $token);
            if (false === $user) {
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

<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Middleware;

use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Users;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Mvc\Micro;

/**
 * Class TokenUserMiddleware
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
        /** @var Cache $cache */
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

<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Middleware;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Users;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Mvc\Micro;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Validator;

/**
 * Class AuthenticationMiddleware
 */
class TokenVerificationMiddleware extends TokenBase
{
    /**
     * @param Micro $api
     *
     * @return bool
     * @throws ModelException
     */
    public function call(Micro $api): bool
    {
        /** @var Cache $cache */
        $cache = $api->getService('cache');
        /** @var Config $config */
        $config = $api->getService('config');
        /** @var Request $request */
        $request = $api->getService('request');
        /** @var Response $response */
        $response = $api->getService('response');
        if (true === $this->isValidCheck($request)) {
            /**
             * This is where we will validate the token that was sent to us
             * using Bearer Authentication
             *
             * Find the user attached to this token
             */
            $token  = $this->getToken($request->getBearerTokenFromHeader());
            $signer = new Hmac();

            /** @var Users $user */
            $user = $this->getUserByToken($config, $cache, $token);
            if (false === $token->verify($signer, $user->get('tokenPassword'))) {
                $this->halt(
                    $api,
                    $response::OK,
                    'Invalid Token (verification)'
                );
            }
        }

        return true;
    }
}

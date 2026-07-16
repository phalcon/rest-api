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

use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

use function implode;

/**
 * Authorizes the request.
 *
 * The bearer token is parsed once and the user resolved once, then the three
 * phases of authorization run in order:
 *
 *   1. is the user carried by the token one that we know?
 *   2. was the token signed with that user's passphrase?
 *   3. do the token's claims match the ones we expect?
 *
 * The login route is exempt - it is what issues the token in the first place.
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    use QueryTrait;
    use ResponseTrait;
    use TokenTrait;

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
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

        if (true === $request->isLoginPage()) {
            return true;
        }

        if (true === $request->isEmptyBearerToken()) {
            $this->halt($api, $response::OK, 'Invalid Token');

            return false;
        }

        $token = $this->getToken($request->getBearerTokenFromHeader());

        /**
         * Phase 1 - is the user attached to this token in the database?
         */
        /** @var Users|null $user */
        $user = $this->getUserByToken($config, $cache, $token);
        if (null === $user) {
            $this->halt($api, $response::OK, 'Invalid token (user)');

            return false;
        }

        /**
         * Phase 2 - was the token signed with this user's passphrase?
         */
        if (false === $token->verify(new Hmac(), $user->get('tokenPassword'))) {
            $this->halt($api, $response::OK, 'Invalid Token (verification)');

            return false;
        }

        /**
         * Phase 3 - do the claims the token carries match this user's?
         */
        $errors = $token->validate($user->getValidationData($token));
        if (true !== empty($errors)) {
            $this->halt(
                $api,
                $response::OK,
                'Invalid Token [' . implode('; ', $errors) . ']'
            );

            return false;
        }

        return true;
    }
}

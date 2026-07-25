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
use Phalcon\Api\Exception\TokenException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Services\TokenService;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Authorizes the request.
 *
 * Whether a token is valid is one question with one answer, so it is asked
 * once, of the one component that owns it. This middleware decides only what
 * an invalid token does to the response - the login route is exempt, since it
 * is what issues the token in the first place.
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    use ResponseTrait;

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     * @throws ModelException
     */
    public function call(Micro $api): bool
    {
        /** @var Request $request */
        $request = $api->getService('request');
        /** @var Response $response */
        $response = $api->getService('response');
        /** @var TokenService $tokenService */
        $tokenService = $api->getService('tokenService');

        if (true === $request->isLoginPage()) {
            return true;
        }

        if (true === $request->isEmptyBearerToken()) {
            $this->halt($api, $response::OK, 'Invalid Token');

            return false;
        }

        try {
            $tokenService->authenticate($request->getBearerTokenFromHeader());
        } catch (TokenException $ex) {
            $this->halt($api, $response::OK, $ex->getMessage());

            return false;
        }

        return true;
    }
}

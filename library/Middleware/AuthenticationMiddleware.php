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
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    use ResponseTrait;
    use QueryTrait;

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
        $request  = $api->getService('request');
        /** @var Response $response */
        $response = $api->getService('response');

        if (true !== $request->isLoginPage() &&
            true === $request->isEmptyBearerToken()) {
            $this->halt(
                $api,
                $response::OK,
                'Invalid Token'
            );

            return false;
        }

        return true;
    }
}

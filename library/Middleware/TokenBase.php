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
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 */
abstract class TokenBase implements MiddlewareInterface
{
    use ResponseTrait;
    use TokenTrait;
    use QueryTrait;

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isValidCheck(Request $request): bool
    {
        return (
            true !== $request->isLoginPage() &&
            true !== $request->isEmptyBearerToken()
        );
    }
}

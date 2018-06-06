<?php

namespace Niden\Middleware;

use Niden\Exception\Exception;
use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Traits\ResponseTrait;
use Niden\Traits\UserTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    use ResponseTrait;
    use UserTrait;

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     * @throws Exception
     */
    public function call(Micro $api)
    {
        try {
            /** @var Request $request */
            $request = $api->getService('request');

            if (true === $request->isPost() &&
                true !== $request->isLoginPage() &&
                true === $request->isEmptyBearerToken()) {
                throw new Exception('Invalid Token');
            }

            return true;
        } catch (Exception $ex) {
            $this->halt($api, $ex->getMessage());

            return false;
        }
    }
}

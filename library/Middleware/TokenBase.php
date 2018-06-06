<?php

namespace Niden\Middleware;

use function time;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\ValidationData;
use Niden\Exception\Exception;
use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Models\Users;
use Niden\Traits\ResponseTrait;
use Niden\Traits\UserTrait;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 *
 * @package Niden\Middleware
 */
abstract class TokenBase implements MiddlewareInterface
{
    use ResponseTrait;
    use UserTrait;

    /**
     * @param string $token
     *
     * @return Users
     * @throws Exception
     */
    protected function getTokenUser(string $token): Users
    {
        /** @var Users|false $user */
        $user = $this->getUserByToken($token);

        if (false === $user) {
            throw new Exception('Invalid Token');
        }

        return $user;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isValidCheck(Request $request): bool
    {
        return (
            true === $request->isPost() &&
            true !== $request->isLoginPage() &&
            true !== $request->isEmptyBearerToken()
        );
    }
}

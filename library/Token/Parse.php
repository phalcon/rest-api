<?php

namespace Niden;

use Lcobucci\JWT\Parser;
use Niden\Exception\Exception;
use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Token\Validation;
use Niden\Traits\UserTrait;

/**
 * Class TokenParser
 *
 * @package Niden
 *
 * @property Request  $request
 * @property Response $response
 */
class TokenParser
{
    use UserTrait;

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    /**
     * TokenParser constructor.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws ModelException|Exception
     */
    public function call(): bool
    {
        $uri = $this->request->getURI();
        if ('/login' === $uri) {
            /**
             * This is where we will validate the token that was sent to us
             * using Bearer Authentication
             */
            $token = $this->request->getBearerTokenFromHeader();
            $user  = $this->getUserByToken($token, 'Invalid Token');

            $tokenObject    = (new Parser())->parse($token);
            $validationData = (new Validation())->get($user);

            $valid = $tokenObject->validate($validationData);
            if (false === $valid) {
                throw new Exception('Invalid Token');
            }
        }

        return true;
    }
}

<?php

namespace Niden;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\UserTrait;
use Phalcon\Http\Request;

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

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function call(): bool
    {
        $uri = $this->request->getURI();
        if ('/login' === $uri) {
            try {
                /**
                 * This is where we will validate the token that was sent to us
                 * using Bearer Authentication
                 */
                $token = $this->getTokenFromHeader();
                $user  = $this->getUserByToken($token, 'Invalid Token');

                $tokenObject    = (new Parser())->parse($token);
                $validationData = $this->getValidationData($user);

                $valid = $tokenObject->validate($validationData);
                if (false === $valid) {
                    throw new Exception('Invalid Token');
                }
            } catch (Exception $ex) {
                $this->response->sendError('Auth', $ex->getMessage());

                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    private function getTokenFromHeader()
    {
        $header = $this->request->getHeader('Authorization');
        $bearer = sscanf($header, 'Bearer %s');

        return $bearer[0] ?? '';
    }

    /**
     * @param Users $user
     *
     * @return ValidationData
     * @throws \Niden\Exception\ModelException
     */
    private function getValidationData(Users $user)
    {
        $validationData = new ValidationData();
        $validationData->setIssuer('phalconphp.com');
        $validationData->setAudience($user->get('usr_domain_name'));
        $validationData->setId($user->get('usr_token_id'));

        return $validationData;
    }
}

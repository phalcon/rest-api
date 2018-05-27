<?php

namespace Niden\Api\Controllers;

use Lcobucci\JWT\Builder;
use Niden\Exception\ModelException;
use Niden\Http\Response;
use Niden\Traits\UserTrait;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class LoginController
 *
 * @package Niden\Api\Controllers
 *
 * @property Response $response
 */
class LoginController extends Controller
{
    use UserTrait;

    /**
     * Default action for integrations
     */
    /**
     * @return array
     * @throws ModelException
     */
    public function indexAction()
    {
        $username = $this->request->getPost('username', Filter::FILTER_STRING);
        $password = $this->request->getPost('password', Filter::FILTER_STRING);

        $user = $this->getUserByUsernameAndPassword(
            $username,
            $password,
            'Incorrect credentials'
        );

        $builder = new Builder();
        $token   = $builder
                        ->setIssuer('http://phalconphp.com')
                        ->setAudience($user->get('usr_domain_name'))
                        ->setId($user->get('usr_token_id'), true)
                        ->setIssuedAt(time())
                        ->setNotBefore(time() + 60)
                        ->setExpiration(time() + 3600)
                        ->getToken();

        $stringToken = $token->__toString();

        $result = $user
                    ->set('usr_token', $stringToken)
                    ->save();
        $this->checkResult($result, 'Cannot update user record');

        /**
         * User found - Return token
         */
        $this->response->setPayloadSuccess(['token' => $stringToken]);
    }
}

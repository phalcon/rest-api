<?php

namespace Niden\Api\Controllers;

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

        /**
         * User found - Return token
         */
        $this->response->setPayloadSuccess(['token' => $user->get('usr_token')]);
    }
}

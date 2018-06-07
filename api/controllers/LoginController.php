<?php

namespace Niden\Api\Controllers;

use function explode;
use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\TokenTrait;
use Niden\Traits\UserTrait;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class LoginController
 *
 * @package Niden\Api\Controllers
 *
 * @property Request  $request
 * @property Response $response
 */
class LoginController extends Controller
{
    use TokenTrait;
    use UserTrait;

    /**
     * Default action logging in
     *
     * @return array
     * @throws ModelException
     */
    public function callAction()
    {
        $username = $this->request->getPost('username', Filter::FILTER_STRING);
        $password = $this->request->getPost('password', Filter::FILTER_STRING);
        $parameters = [
            'usr_username' => $username,
            'usr_password' => $password,
        ];
        /** @var Users|false $user */
        $user = $this->getUser($parameters);

        if (false !== $user) {
            /**
             * User found - Return token
             */
            return ['token' => $user->getToken()];
        } else {
            $this->response->setPayloadError('Incorrect credentials');
        }
    }
}

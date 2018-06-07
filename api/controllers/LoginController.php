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
            $token = $user->getToken();
            $this->updateRecord($user, $token);

            /**
             * User found - Return token
             */
            return ['token' => $token];
        } else {
            $this->response->setPayloadError('Incorrect credentials');
        }
    }

    /**
     * @param Users  $user
     * @param string $token
     *
     * @throws ModelException
     */
    private function updateRecord(Users $user, string $token)
    {
        list($pre, $mid, $post) = explode('.', $token);
        $result = $user
            ->set('usr_token_pre', $pre)
            ->set('usr_token_mid', $mid)
            ->set('usr_token_post', $post)
            ->save();

        if (false === $result) {
            throw new ModelException('Cannot update user record');
        }
    }
}

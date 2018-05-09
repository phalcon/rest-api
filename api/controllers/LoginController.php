<?php

namespace Niden\Api\Controllers;

use Niden\Http\Response;
use Niden\Models\Users;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * Class LoginController
 *
 * @package Niden\Api\Controllers
 *
 * @property Response $response
 */
class LoginController extends Controller
{
    /**
     * Default action for integrations
     */
    public function indexAction()
    {
        $userName = $this->request->getPost('user', Filter::FILTER_STRING);
        $password = $this->request->getPost('pass', Filter::FILTER_STRING);

        /**
         * Get the user
         */
        $user = Users::findFirst(
            [
                'conditions' => 'usr_username = :usr_username: AND '
                             .  'usr_password = :usr_password:',
                'bind'       => [
                    'usr_username' => $userName,
                    'usr_password' => $password,
                ]
            ]
        );

        /**
         * User not found
         */
        if (false === $user) {
            $this
                ->response
                ->setPayloadStatusError()
                ->setErrorSource('Login')
                ->setErrorDetail('Incorrect credentials')
            ;
        } else {
            /**
             * User found - Return token
             */

            return 'mytoken';
        }
    }
}

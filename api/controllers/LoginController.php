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

        $buikder = new Builder();
        $user    = $buikder
            ->addFrom(Users::class)
            ->andWhere('usr_username = :u:', ['u' => $userName])
            ->andWhere('usr_password = :p:', ['p' => $password])
            ->getQuery()
            ->setUniqueRow(true)
            ->execute();

        /**
         * User not found
         */
        if (false === $user) {
            $this
                ->response
                ->setError('Login', 'Incorrect credentials')
            ;
        } else {
            /**
             * User found - Return token
             */

            return 'mytoken';
        }
    }
}

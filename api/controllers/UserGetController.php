<?php

namespace Niden\Api\Controllers;

use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\UserTrait;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Users
 *
 * @property Response $response
 */
class UserGetController extends Controller
{
    use UserTrait;

    /**
     * Get a user
     *
     * @throws Exception
     */
    public function getAction()
    {
        /** @var int $userId */
        $userId = $this->request->getPost('userId', Filter::FILTER_ABSINT, 0);
        /** @var Users|false $user */
        $user   = Users::findFirst(
            [
                'conditions' => 'usr_id = :usr_id:',
                'bind'       => [
                    'usr_id' => $userId,
                ]
            ]
        );

        if (false === $user) {
            throw new Exception('User not found');
        }

        /**
         * User found - Return token
         */
        $this->response->setPayloadSuccess($user->getApiRecord());
    }
}

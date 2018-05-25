<?php

namespace Niden\Api\Controllers;

use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Traits\UserTrait;
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
     * Default action
     */
    public function getAction()
    {
        try {
            /**
             * User found - Return token
             */
            return 'Hello';
        } catch (Exception $ex) {
            $this->response->setPayloadError('User', $ex->getMessage());
        }
    }
}

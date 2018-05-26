<?php

namespace Niden\Api\Controllers;

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
        /**
         * User found - Return token
         */
        return 'Hello';
    }
}

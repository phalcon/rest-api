<?php

namespace Niden\Api\Controllers\Users;

use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Traits\FractalTrait;
use Niden\Traits\UserTrait;
use Niden\Transformers\UsersTransformer;
use Phalcon\Mvc\Controller;

/**
 * Class GetManyController
 *
 * @package Niden\Api\Controllers\Users
 *
 * @property Response $response
 */
class GetManyController extends Controller
{
    use FractalTrait;
    use UserTrait;

    /**
     * Gets many users
     *
     * @throws Exception
     */
    public function callAction()
    {
        /**
         * @todo decide what filters we will have
         */
        $results = $this->getUsers();
        $data    = $this->format($results, UsersTransformer::class);

        /**
         * User found - Return token
         */
        $this->response->setPayloadSuccess($data);
    }
}

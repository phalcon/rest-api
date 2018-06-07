<?php

namespace Niden\Api\Controllers\Users;

use Niden\Http\Response;
use Niden\Traits\FractalTrait;
use Niden\Traits\ResponseTrait;
use Niden\Traits\UserTrait;
use Niden\Transformers\UsersTransformer;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro;

/**
 * Class GetManyController
 *
 * @package Niden\Api\Controllers\Users
 *
 * @property Micro    $application
 * @property Response $response
 */
class GetManyController extends Controller
{
    use FractalTrait;
    use ResponseTrait;
    use UserTrait;

    /**
     * Gets many users
     */
    public function callAction()
    {
        /**
         * @todo decide what filters we will have
         */
        return $this->format($this->getUsers(), UsersTransformer::class);
    }
}

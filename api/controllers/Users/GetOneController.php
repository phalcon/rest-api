<?php

namespace Niden\Api\Controllers\Users;

use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Traits\FractalTrait;
use Niden\Traits\UserTrait;
use Niden\Transformers\UsersTransformer;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class GetOneController
 *
 * @package Niden\Api\Controllers\Users
 *
 * @property Response $response
 */
class GetOneController extends Controller
{
    use FractalTrait;
    use UserTrait;

    /**
     * Get a user
     *
     * @throws Exception
     */
    public function callAction()
    {
        /** @var int $userId */
        $userId = $this->request->getPost('userId', Filter::FILTER_ABSINT, 0);

        $parameters = [
            'usr_id' => $userId,
        ];
        $results    = $this->getUsers($parameters);
        $data       = $this->format($results, UsersTransformer::class);

        /**
         * User found - Return token
         */
        $this->response->setPayloadSuccess($data);
    }
}

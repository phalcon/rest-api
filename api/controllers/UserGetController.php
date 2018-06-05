<?php

namespace Niden\Api\Controllers;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\UserTrait;
use Niden\Transformers\UsersTransformer;
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
        $user   = Users::find(
            [
                'conditions' => 'usr_id = :usr_id:',
                'bind'       => [
                    'usr_id' => $userId,
                ]
            ]
        );

        if (0 === count($user)) {
            throw new Exception('User not found');
        }

        /**
         * Transform the record
         */
        $manager  = new Manager();
        $resource = new Collection($user, new UsersTransformer());
        $data     = $manager->createData($resource)->toArray();

        /**
         * User found - Return token
         */
        $this->response->setPayloadSuccess($data);
    }
}

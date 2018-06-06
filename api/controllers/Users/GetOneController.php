<?php

namespace Niden\Api\Controllers\Users;

use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Traits\FractalTrait;
use Niden\Traits\ResponseTrait;
use Niden\Traits\UserTrait;
use Niden\Transformers\UsersTransformer;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro;

/**
 * Class GetOneController
 *
 * @package Niden\Api\Controllers\Users
 *
 * @property Micro    $application
 * @property Response $response
 */
class GetOneController extends Controller
{
    use FractalTrait;
    use ResponseTrait;
    use UserTrait;

    /**
     * Get a user
     */
    public function callAction()
    {
        try {
            /** @var int $userId */
            $userId     = $this->request->getPost('userId', Filter::FILTER_ABSINT, 0);
            $parameters = ['usr_id' => $userId];

            return $this->format(
                $this->getUsers($parameters, true),
                UsersTransformer::class
            );
        } catch (Exception $ex) {
            $this->halt($this->application, $ex->getMessage());
        }
    }
}

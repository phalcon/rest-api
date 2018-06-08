<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Users;

use Niden\Http\Request;
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
 * @property Request  $request
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
        /** @var int $userId */
        $userId     = $this->request->getPost('userId', Filter::FILTER_ABSINT, 0);
        $parameters = ['usr_id' => $userId];
        $results    = $this->getUsers($parameters);

        if (count($results) > 0) {
            return $this->format($results, UsersTransformer::class);
        } else {
            $this->halt($this->application, 'Record not found');
        }
    }
}

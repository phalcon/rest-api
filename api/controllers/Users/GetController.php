<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Users;

use Niden\Models\Users;
use Niden\Traits\FractalTrait;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Niden\Transformers\UsersTransformer;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Users
 */
class GetController extends Controller
{
    use FractalTrait;
    use ResponseTrait;
    use QueryTrait;

    /**
     * Gets users
     */
    public function callAction()
    {
        $parameters = $this->checkParameters();

        /**
         * Execute the query
         */
        $results = $this->getRecords(Users::class, $parameters);

        return $this->format($results, UsersTransformer::class);
    }

    /**
     * Checks the passed parameters and returns the relevant array back
     *
     * @return array
     */
    private function checkParameters(): array
    {
        $parameters = [];

        /** @var int $userId */
        $userId = $this->request->getPost('userId', Filter::FILTER_ABSINT, 0);

        if ($userId > 0) {
            $parameters['usr_id'] = $userId;
        }

        return $parameters;
    }
}

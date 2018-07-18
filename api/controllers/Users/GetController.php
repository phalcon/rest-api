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
     *
     * @param int $userId
     *
     * @return array
     */
    public function callAction($userId = 0)
    {
        $parameters = $this->checkParameters($userId);

        /**
         * Execute the query
         */
        $results = $this->getRecords(Users::class, $parameters);

        return $this->format($results, UsersTransformer::class);
    }

    /**
     * Checks the passed parameters and returns the relevant array back
     *
     * @param int $userId
     *
     * @return array
     */
    private function checkParameters($userId = 0): array
    {
        $parameters = [];

        /** @var int $localUserId */
        $localUserId = $this->filter->sanitize($userId, Filter::FILTER_ABSINT);

        if ($localUserId > 0) {
            $parameters['usr_id'] = $localUserId;
        }

        return $parameters;
    }
}

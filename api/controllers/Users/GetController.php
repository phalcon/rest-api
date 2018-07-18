<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Users;

use Niden\Api\Controllers\BaseController;
use Niden\Models\Users;
use Niden\Traits\FractalTrait;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Niden\Transformers\UsersTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Users
 */
class GetController extends BaseController
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
        $parameters = $this->checkIdParameter('usr_id', $userId);
        $results    = $this->getRecords(Users::class, $parameters, 'usr_username');

        return $this->format($results, UsersTransformer::class);
    }
}

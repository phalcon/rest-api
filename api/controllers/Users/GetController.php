<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Users;

use Niden\Api\Controllers\BaseController;
use Niden\Models\Users;
use Niden\Transformers\UsersTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Users
 */
class GetController extends BaseController
{
    /**
     * Gets users
     *
     * @param int $userId
     *
     * @return array
     */
    public function callAction($userId = 0)
    {
        return $this->processCall(
            Users::class,
            'usr_id',
            UsersTransformer::class,
            $userId,
            'usr_username'
        );
    }
}

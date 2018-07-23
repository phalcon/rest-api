<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Users;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Resources;
use Niden\Models\Users;
use Niden\Transformers\BaseTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Users
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = Users::class;
    /** @var string */
    protected $resource    = Resources::USERS;
    /** @var string */
    protected $transformer = BaseTransformer::class;
    /** @var string */
    protected $orderBy     = 'username';
}

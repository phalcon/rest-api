<?php

declare(strict_types=1);

namespace Phalcon\Api\Api\Controllers\Users;

use Phalcon\Api\Api\Controllers\BaseController;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Transformers\BaseTransformer;

/**
 * Class GetController
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = Users::class;

    /** @var string */
    protected $resource    = Relationships::USERS;

    /** @var string */
    protected $transformer = BaseTransformer::class;

    /** @var array<string,bool> */
    protected $sortFields  = [
        'id'            => true,
        'status'        => true,
        'username'      => true,
        'password'      => false,
        'issuer'        => true,
        'tokenPassword' => false,
        'tokenId'       => false,
    ];

    /** @var string */
    protected $orderBy     = 'username';
}

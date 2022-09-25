<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
    protected string $model = Users::class;

    /** @var string */
    protected string $resource = Relationships::USERS;

    /** @var string */
    protected string $transformer = BaseTransformer::class;

    /** @var array<string,bool> */
    protected array $sortFields = [
        'id'            => true,
        'status'        => true,
        'username'      => true,
        'password'      => false,
        'issuer'        => true,
        'tokenPassword' => false,
        'tokenId'       => false,
    ];

    /** @var string */
    protected string $orderBy = 'username';
}

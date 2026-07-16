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

namespace Phalcon\Api\Api\Controllers\ProductTypes;

use Phalcon\Api\Api\Controllers\BaseController;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\ProductTypes;
use Phalcon\Api\Transformers\ProductTypesTransformer;

/**
 * Class GetController
 */
class GetController extends BaseController
{
    /** @var string */
    protected string $model = ProductTypes::class;

    /** @var array */
    protected array $includes = [
        Relationships::PRODUCTS,
    ];

    /** @var string */
    protected string $resource = Relationships::PRODUCT_TYPES;

    /** @var array<string,bool> */
    protected array $sortFields = [
        'id'          => true,
        'name'        => true,
        'description' => false,
    ];

    /** @var string */
    protected string $transformer = ProductTypesTransformer::class;
}

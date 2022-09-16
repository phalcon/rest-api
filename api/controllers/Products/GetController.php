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

namespace Phalcon\Api\Api\Controllers\Products;

use Phalcon\Api\Api\Controllers\BaseController;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Transformers\ProductsTransformer;

/**
 * Class GetController
 */
class GetController extends BaseController
{
    /** @var string */
    protected string $model = Products::class;

    /** @var array */
    protected array $includes = [
        Relationships::COMPANIES,
        Relationships::PRODUCT_TYPES,
    ];

    /** @var string */
    protected string $resource = Relationships::PRODUCTS;

    /** @var array<string,bool> */
    protected array $sortFields = [
        'id'          => true,
        'typeId'      => true,
        'name'        => true,
        'description' => false,
        'quantity'    => true,
        'price'       => true,
    ];

    /** @var string */
    protected string $transformer = ProductsTransformer::class;
}

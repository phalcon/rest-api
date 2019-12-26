<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
    protected $model       = Products::class;

    /** @var array */
    protected $includes    = [
        Relationships::COMPANIES,
        Relationships::PRODUCT_TYPES,
    ];

    /** @var string */
    protected $resource    = Relationships::PRODUCTS;

    /** @var array<string,bool> */
    protected $sortFields  = [
        'id'          => true,
        'typeId'      => true,
        'name'        => true,
        'description' => false,
        'quantity'    => true,
        'price'       => true,
    ];

    /** @var string */
    protected $transformer = ProductsTransformer::class;
}

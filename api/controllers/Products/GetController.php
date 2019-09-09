<?php

declare(strict_types=1);

namespace Phalcon\Api\Api\Controllers\Products;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Relationships;
use Niden\Models\Products;
use Niden\Transformers\ProductsTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Products
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

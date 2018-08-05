<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\ProductTypes;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Relationships;
use Niden\Models\ProductTypes;
use Niden\Transformers\ProductTypesTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\ProductTypes
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = ProductTypes::class;

    /** @var array */
    protected $includes    = [
        Relationships::PRODUCTS,
    ];

    /** @var string */
    protected $resource    = Relationships::PRODUCT_TYPES;

    /** @var array<string,bool> */
    protected $sortFields  = [
        'id'          => true,
        'name'        => true,
        'description' => false,
    ];

    /** @var string */
    protected $transformer = ProductTypesTransformer::class;
}


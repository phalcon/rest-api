<?php

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


<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\ProductTypes;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Resources;
use Niden\Models\ProductTypes;
use Niden\Transformers\BaseTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\IndividualTypes
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = ProductTypes::class;
    /** @var string */
    protected $resource    = Resources::PRODUCT_TYPES;
    /** @var string */
    protected $transformer = BaseTransformer::class;
}


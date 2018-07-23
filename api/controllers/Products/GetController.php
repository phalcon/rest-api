<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Products;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Resources;
use Niden\Models\Products;
use Niden\Transformers\BaseTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Companies
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = Products::class;
    /** @var string */
    protected $resource    = Resources::PRODUCTS;
    /** @var string */
    protected $transformer = BaseTransformer::class;
}

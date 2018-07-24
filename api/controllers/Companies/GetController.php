<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Companies;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Resources;
use Niden\Models\Companies;
use Niden\Transformers\BaseTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Companies
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model         = Companies::class;

    /** @var array */
    protected $relationships = [
        Resources::INDIVIDUALS,
        Resources::PRODUCTS,
    ];

    /** @var string */
    protected $resource      = Resources::COMPANIES;

    /** @var string */
    protected $transformer   = BaseTransformer::class;
}

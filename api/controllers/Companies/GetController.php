<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Companies;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Transformers\CompaniesTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Companies
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = Companies::class;

    /** @var array */
    protected $includes    = [
        Relationships::INDIVIDUALS,
        Relationships::PRODUCTS,
    ];

    /** @var string */
    protected $resource    = Relationships::COMPANIES;

    /** @var array<string|boolean> */
    protected $sortFields  = [
        'id'      => true,
        'name'    => true,
        'address' => true,
        'city'    => true,
        'phone'   => true,
    ];

    /** @var string */
    protected $transformer = CompaniesTransformer::class;
}

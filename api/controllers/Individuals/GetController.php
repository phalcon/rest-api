<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Individuals;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Relationships;
use Niden\Models\Individuals;
use Niden\Transformers\IndividualsTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Individuals
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = Individuals::class;

    /** @var array */
    protected $includes    = [
        Relationships::COMPANIES,
        Relationships::INDIVIDUAL_TYPES,
    ];

    /** @var string */
    protected $resource    = Relationships::INDIVIDUALS;

    /** @var string */
    protected $transformer = IndividualsTransformer::class;

    /** @var string */
    protected $orderBy     = 'last, first';
}

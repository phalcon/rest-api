<?php

declare(strict_types=1);

namespace Phalcon\Api\Api\Controllers\IndividualTypes;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Relationships;
use Niden\Models\IndividualTypes;
use Niden\Transformers\IndividualTypesTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\IndividualTypes
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = IndividualTypes::class;

    /** @var array */
    protected $includes    = [
        Relationships::INDIVIDUALS,
    ];

    /** @var string */
    protected $resource    = Relationships::INDIVIDUAL_TYPES;

    /** @var array<string,bool> */
    protected $sortFields  = [
        'id'          => true,
        'name'        => true,
        'description' => false,
    ];

    /** @var string */
    protected $transformer = IndividualTypesTransformer::class;
}


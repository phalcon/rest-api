<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\IndividualTypes;

use Niden\Api\Controllers\BaseController;
use Niden\Constants\Resources;
use Niden\Models\IndividualTypes;
use Niden\Transformers\BaseTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\IndividualTypes
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = IndividualTypes::class;
    /** @var string */
    protected $resource    = Resources::INDIVIDUAL_TYPES;
    /** @var string */
    protected $transformer = BaseTransformer::class;
}


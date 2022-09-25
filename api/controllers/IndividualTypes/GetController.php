<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Api\Controllers\IndividualTypes;

use Phalcon\Api\Api\Controllers\BaseController;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Api\Transformers\IndividualTypesTransformer;

/**
 * Class GetController
 */
class GetController extends BaseController
{
    /** @var string */
    protected string $model = IndividualTypes::class;

    /** @var array */
    protected array $includes = [
        Relationships::INDIVIDUALS,
    ];

    /** @var string */
    protected string $resource = Relationships::INDIVIDUAL_TYPES;

    /** @var array<string,bool> */
    protected array $sortFields = [
        'id'          => true,
        'name'        => true,
        'description' => false,
    ];

    /** @var string */
    protected string $transformer = IndividualTypesTransformer::class;
}

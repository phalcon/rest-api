<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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

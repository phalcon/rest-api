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

namespace Phalcon\Api\Api\Controllers\Individuals;

use Phalcon\Api\Api\Controllers\BaseController;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Transformers\IndividualsTransformer;

/**
 * Class GetController
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

    /** @var array<string,bool> */
    protected $sortFields  = [
        'id'        => true,
        'companyId' => true,
        'typeId'    => true,
        'prefix'    => true,
        'first'     => true,
        'middle'    => true,
        'last'      => true,
        'suffix'    => true,
    ];

    /** @var string */
    protected $orderBy     = 'last, first';
}

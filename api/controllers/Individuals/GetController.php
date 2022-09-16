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
    protected string $model = Individuals::class;

    /** @var array */
    protected array $includes = [
        Relationships::COMPANIES,
        Relationships::INDIVIDUAL_TYPES,
    ];

    /** @var string */
    protected string $resource = Relationships::INDIVIDUALS;

    /** @var string */
    protected string $transformer = IndividualsTransformer::class;

    /** @var array<string,bool> */
    protected array $sortFields = [
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
    protected string $orderBy = 'last, first';
}

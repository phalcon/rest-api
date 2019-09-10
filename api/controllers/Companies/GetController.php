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

namespace Phalcon\Api\Api\Controllers\Companies;

use Phalcon\Api\Api\Controllers\BaseController;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Transformers\CompaniesTransformer;

/**
 * Class GetController
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

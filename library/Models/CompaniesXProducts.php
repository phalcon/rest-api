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

namespace Phalcon\Api\Models;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Filter;

/**
 * Class CompaniesXProducts
 */
class CompaniesXProducts extends AbstractModel
{
    /**
     * Initialize relationships and model properties
     */
    public function initialize()
    {
        $this->setSource('co_companies_x_products');

        $this->belongsTo(
            'companyId',
            Companies::class,
            'id',
            [
                'alias'    => Relationships::COMPANIES,
                'reusable' => true,
            ]
        );

        $this->belongsTo(
            'productId',
            Products::class,
            'id',
            [
                'alias'    => Relationships::PRODUCTS,
                'reusable' => true,
            ]
        );

        parent::initialize();
    }

    /**
     * Model filters
     *
     * @return array<string,string>
     */
    public function getModelFilters(): array
    {
        return [
            'companyId' => Filter::FILTER_ABSINT,
            'productId' => Filter::FILTER_ABSINT,
        ];
    }
}

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
 * Class Products
 */
class Products extends AbstractModel
{
    /**
     * Initialize relationships and model properties
     */
    public function initialize()
    {
        $this->setSource('co_products');

        $this->hasManyToMany(
            'id',
            CompaniesXProducts::class,
            'productId',
            'companyId',
            Companies::class,
            'id',
            [
                'alias'    => Relationships::COMPANIES,
                'reusable' => true,
            ]
        );

        $this->belongsTo(
            'typeId',
            ProductTypes::class,
            'id',
            [
                'alias'    => Relationships::PRODUCT_TYPES,
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
            'id'          => Filter::FILTER_ABSINT,
            'typeId'      => Filter::FILTER_ABSINT,
            'name'        => Filter::FILTER_STRING,
            'description' => Filter::FILTER_STRING,
            'quantity'    => Filter::FILTER_ABSINT,
            'price'       => Filter::FILTER_FLOAT,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Niden\Models;

use Niden\Constants\Relationships;
use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter\Filter;

/**
 * Class Products
 *
 * @package Niden\Models
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

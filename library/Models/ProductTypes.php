<?php

declare(strict_types=1);

namespace Phalcon\Api\Models;

use Niden\Constants\Relationships;
use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter;

/**
 * Class ProductTypes
 *
 * @package Niden\Models
 */
class ProductTypes extends AbstractModel
{
    /**
     * Initialize relationships and model properties
     */
    public function initialize()
    {
        $this->hasMany(
            'id',
            Products::class,
            'typeId',
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
            'id'          => Filter::FILTER_ABSINT,
            'name'        => Filter::FILTER_STRING,
            'description' => Filter::FILTER_STRING,
        ];
    }

    /**
     * Returns the source table from the database
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'co_product_types';
    }
}

<?php

declare(strict_types=1);

namespace Niden\Models;

use Niden\Constants\Relationships;
use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter;

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
        $this->belongsTo(
            'prd_com_id',
            Companies::class,
            'com_id',
            [
                'alias'    => Relationships::COMPANY,
                'reusable' => true,
            ]
        );

        $this->hasOne(
            'prd_prt_id',
            ProductTypes::class,
            'prt_id',
            [
                'alias'    => Relationships::PRODUCT_TYPE,
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
            'prd_id'          => Filter::FILTER_ABSINT,
            'prd_prt_id'      => Filter::FILTER_ABSINT,
            'prd_name'        => Filter::FILTER_STRING,
            'prd_description' => Filter::FILTER_STRING,
            'prd_quantity'    => Filter::FILTER_ABSINT,
            'prd_price'       => Filter::FILTER_FLOAT,
        ];
    }

    /**
     * Returns the source table from the database
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'co_products';
    }

    /**
     * Table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return 'prd';
    }
}

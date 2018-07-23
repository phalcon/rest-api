<?php

declare(strict_types=1);

namespace Niden\Models;

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
        $this->belongsTo(
            'id',
            Products::class,
            'typeId',
            [
                'alias'    => Relationships::PRODUCT,
                'reusable' => true,
            ]
        );

        parent::initialize();
    }

    /**
     * Column Map
     *
     * @return array<string,string>
     */
    public function columnMap(): array
    {
        return [
            'prt_id'          => 'id',
            'prt_name'        => 'name',
            'prt_description' => 'description',
        ];
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

    /**
     * Table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return 'prt';
    }
}

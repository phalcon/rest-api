<?php

declare(strict_types=1);

namespace Niden\Models;

use Niden\Traits\TokenTrait;
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
     * Model filters
     *
     * @return array<string,string>
     */
    public function getModelFilters(): array
    {
        return [
            'prd_id'          => Filter::FILTER_ABSINT,
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

<?php

declare(strict_types=1);

namespace Phalcon\Api\Models;

use Niden\Constants\Relationships;
use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter;

/**
 * Class CompaniesXProducts
 *
 * @package Niden\Models
 */
class CompaniesXProducts extends AbstractModel
{
    /**
     * Initialize relationships and model properties
     */
    public function initialize()
    {
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

    /**
     * Returns the source table from the database
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'co_companies_x_products';
    }
}

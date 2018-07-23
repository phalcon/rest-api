<?php

declare(strict_types=1);

namespace Niden\Models;

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
            'cxp_com_id',
            Companies::class,
            'id',
            [
                'alias'    => Relationships::COMPANY,
                'reusable' => true,
            ]
        );

        $this->belongsTo(
            'cxp_prd_id',
            Products::class,
            'id',
            [
                'alias'    => Relationships::PRODUCT,
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
            'cxp_com_id' => Filter::FILTER_ABSINT,
            'cxp_prd_id' => Filter::FILTER_ABSINT,
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

    /**
     * Table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return 'cxp';
    }
}

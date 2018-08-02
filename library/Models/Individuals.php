<?php

declare(strict_types=1);

namespace Niden\Models;

use Niden\Constants\Relationships;
use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter;

/**
 * Class Individuals
 *
 * @package Niden\Models
 */
class Individuals extends AbstractModel
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
                'alias'    => Relationships::COMPANY,
                'reusable' => true,
            ]
        );

        $this->hasOne(
            'typeId',
            IndividualTypes::class,
            'id',
            [
                'alias'    => Relationships::INDIVIDUAL_TYPES,
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
            'id'        => Filter::FILTER_ABSINT,
            'companyId' => Filter::FILTER_ABSINT,
            'typeId'    => Filter::FILTER_ABSINT,
            'prefix'    => Filter::FILTER_STRING,
            'first'     => Filter::FILTER_STRING,
            'middle'    => Filter::FILTER_STRING,
            'last'      => Filter::FILTER_STRING,
            'suffix'    => Filter::FILTER_STRING,
        ];
    }

    /**
     * Returns the source table from the database
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'co_individuals';
    }
}

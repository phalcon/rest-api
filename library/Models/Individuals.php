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
            'ind_com_id',
            Companies::class,
            'com_id',
            [
                'alias'    => Relationships::COMPANY,
                'reusable' => true,
            ]
        );

        $this->hasOne(
            'ind_idt_id',
            IndividualTypes::class,
            'idt_id',
            [
                'alias'    => Relationships::INDIVIDUAL_TYPE,
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
            'ind_id'          => Filter::FILTER_ABSINT,
            'ind_com_id'      => Filter::FILTER_ABSINT,
            'ind_idt_id'      => Filter::FILTER_ABSINT,
            'ind_name_prefix' => Filter::FILTER_STRING,
            'ind_name_first'  => Filter::FILTER_STRING,
            'ind_name_middle' => Filter::FILTER_STRING,
            'ind_name_last'   => Filter::FILTER_STRING,
            'ind_name_suffix' => Filter::FILTER_STRING,
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

    /**
     * Table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return 'ind';
    }
}

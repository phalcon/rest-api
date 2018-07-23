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
     * Column Map
     *
     * @return array<string,string>
     */
    public function columnMap(): array
    {
        return [
            'ind_id'          => 'id',
            'ind_com_id'      => 'companyId',
            'ind_idt_id'      => 'typeId',
            'ind_name_prefix' => 'prefix',
            'ind_name_first'  => 'first',
            'ind_name_middle' => 'middle',
            'ind_name_last'   => 'last',
            'ind_name_suffix' => 'suffix',
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

<?php

declare(strict_types=1);

namespace Niden\Models;

use Niden\Constants\Relationships;
use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter;

/**
 * Class IndividualTypes
 *
 * @package Niden\Models
 */
class IndividualTypes extends AbstractModel
{
    /**
     * Initialize relationships and model properties
     */
    public function initialize()
    {
        $this->belongsTo(
            'idt_id',
            Individuals::class,
            'ind_idt_id',
            [
                'alias'    => Relationships::INDIVIDUAL,
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
            'idt_id'          => Filter::FILTER_ABSINT,
            'idt_name'        => Filter::FILTER_STRING,
            'idt_description' => Filter::FILTER_STRING,
        ];
    }

    /**
     * Returns the source table from the database
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'co_individual_types';
    }

    /**
     * Table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return 'idt';
    }
}

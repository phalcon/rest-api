<?php

declare(strict_types=1);

namespace Niden\Models;

use Niden\Traits\TokenTrait;
use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter;

/**
 * Class Companies
 *
 * @package Niden\Models
 */
class Companies extends AbstractModel
{
    use TokenTrait;

    /**
     * Model filters
     *
     * @return array<string,string>
     */
    public function getModelFilters(): array
    {
        return [
            'com_id'        => Filter::FILTER_ABSINT,
            'com_name'      => Filter::FILTER_STRING,
            'com_address'   => Filter::FILTER_STRING,
            'com_city'      => Filter::FILTER_STRING,
            'com_telephone' => Filter::FILTER_STRING,
        ];
    }

    /**
     * Returns the source table from the database
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'co_companies';
    }

    /**
     * Table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return 'com';
    }
}

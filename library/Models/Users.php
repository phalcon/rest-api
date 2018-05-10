<?php

namespace Niden\Models;

use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter;

/**
 * Class Users
 *
 * @package Niden\Models
 */
class Users extends AbstractModel
{
    /**
     * Model filters
     *
     * @return array<string,string>
     */
    public function getModelFilters(): array
    {
        return [
            'usr_id'          => Filter::FILTER_ABSINT,
            'usr_status_flag' => Filter::FILTER_ABSINT,
            'usr_username'    => Filter::FILTER_STRING,
            'usr_password'    => Filter::FILTER_STRING,
            'usr_domain_name' => Filter::FILTER_STRING,
            'usr_token'       => Filter::FILTER_STRING,
        ];
    }

    /**
     * Returns the source table from the database
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'co_users';
    }

    /**
     * Table prefix
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return 'usr';
    }
}

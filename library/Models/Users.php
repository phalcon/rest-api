<?php

namespace Niden\Models;

use Niden\Exception\ModelException;
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
     * Returns the record in an array format; used by the API calls
     *
     * @return array
     * @throws ModelException
     */
    public function getApiRecord(): array
    {
        return [
            'id'            => $this->get('usr_id'),
            'status'        => $this->get('usr_status_flag'),
            'username'      => $this->get('usr_username'),
            'domainName'    => $this->get('usr_domain_name'),
            'tokenPassword' => $this->get('usr_token_password'),
            'tokenId'       => $this->get('usr_token_id'),
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
            'usr_id'             => Filter::FILTER_ABSINT,
            'usr_status_flag'    => Filter::FILTER_ABSINT,
            'usr_username'       => Filter::FILTER_STRING,
            'usr_password'       => Filter::FILTER_STRING,
            'usr_domain_name'    => Filter::FILTER_STRING,
            'usr_token_password' => Filter::FILTER_STRING,
            'usr_token_pre'      => Filter::FILTER_STRING,
            'usr_token_mid'      => Filter::FILTER_STRING,
            'usr_token_post'     => Filter::FILTER_STRING,
            'usr_token_id'       => Filter::FILTER_STRING,
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

<?php

namespace Niden\Models;

use Lcobucci\JWT\ValidationData;
use function Niden\Core\envValue;
use Niden\Exception\ModelException;
use Niden\Mvc\Model\AbstractModel;
use Phalcon\Filter;
use function time;

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

    /**
     * Returns the ValidationData object for this record (JWT)
     *
     * @return ValidationData
     * @throws ModelException
     */
    public function getValidationData(): ValidationData
    {
        $validationData = new ValidationData();
        $validationData->setIssuer($this->get('usr_domain_name'));
        $validationData->setAudience(envValue('TOKEN_AUDIENCE', 'https://phalconphp.com'));
        $validationData->setId($this->get('usr_token_id'));
        $validationData->setCurrentTime(time() + 10);

        return $validationData;
    }
}

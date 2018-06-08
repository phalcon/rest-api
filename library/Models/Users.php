<?php

declare(strict_types=1);

namespace Niden\Models;

use function time;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Niden\Traits\TokenTrait;
use Lcobucci\JWT\ValidationData;
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
    use TokenTrait;

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
     * Returns the string token
     *
     * @return string
     * @throws ModelException
     */
    public function getToken(): string
    {
        $signer  = new Sha512();
        $builder = new Builder();
        $token   = $builder
            ->setIssuer($this->get('usr_domain_name'))
            ->setAudience($this->getTokenAudience())
            ->setId($this->get('usr_token_id'), true)
            ->setIssuedAt($this->getTokenTimeIssuedAt())
            ->setNotBefore($this->getTokenTimeNotBefore())
            ->setExpiration($this->getTokenTimeExpiration())
            ->sign($signer, $this->get('usr_token_password'))
            ->getToken();

        return $token->__toString();
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
        $validationData->setAudience($this->getTokenAudience());
        $validationData->setId($this->get('usr_token_id'));
        $validationData->setCurrentTime(time() + 10);

        return $validationData;
    }
}

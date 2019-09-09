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
            'id'            => Filter::FILTER_ABSINT,
            'status'        => Filter::FILTER_ABSINT,
            'username'      => Filter::FILTER_STRING,
            'password'      => Filter::FILTER_STRING,
            'issuer'        => Filter::FILTER_STRING,
            'tokenPassword' => Filter::FILTER_STRING,
            'tokenId'       => Filter::FILTER_STRING,
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
            ->setIssuer($this->get('issuer'))
            ->setAudience($this->getTokenAudience())
            ->setId($this->get('tokenId'), true)
            ->setIssuedAt($this->getTokenTimeIssuedAt())
            ->setNotBefore($this->getTokenTimeNotBefore())
            ->setExpiration($this->getTokenTimeExpiration())
            ->sign($signer, $this->get('tokenPassword'))
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
        $validationData->setIssuer($this->get('issuer'));
        $validationData->setAudience($this->getTokenAudience());
        $validationData->setId($this->get('tokenId'));
        $validationData->setCurrentTime(time() + 10);

        return $validationData;
    }
}

<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Models;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\ValidationData;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Filter;
use function time;

/**
 * Class Users
 */
class Users extends AbstractModel
{
    use TokenTrait;

    /**
     * Returns the source table from the database
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('co_users');
    }

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

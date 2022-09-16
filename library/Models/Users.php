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

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Filter\Filter;
use Phalcon5\Encryption\Security\JWT\Builder;
use Phalcon5\Encryption\Security\JWT\Exceptions\ValidatorException;
use Phalcon5\Encryption\Security\JWT\Signer\Hmac;
use Phalcon5\Encryption\Security\JWT\Token\Token;
use Phalcon5\Encryption\Security\JWT\Validator;

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
        $token = $this->getBuilderToken();

        return $token->getToken();
    }

    /**
     * Returns the Validator object for this record (JWT)
     *
     * @return Validator
     * @throws ModelException
     */
    public function getValidationData(): Validator
    {
        $token = $this->getBuilderToken();

        return new Validator($token, 10);
    }

    /**
     * @return Builder
     * @throws ModelException
     * @throws ValidatorException
     */
    private function getBuilderToken(): Token
    {
        $signer  = new Hmac();
        $builder = new Builder($signer);

        return $builder
            ->setIssuer($this->get('issuer'))
            ->setAudience($this->getTokenAudience())
            ->setId($this->get('tokenId'))
            ->setIssuedAt($this->getTokenTimeIssuedAt())
            ->setNotBefore($this->getTokenTimeNotBefore())
            ->setExpirationTime($this->getTokenTimeExpiration())
            ->setPassphrase($this->get('tokenPassword'))
            ->getToken()
        ;
    }
}

<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Models;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Filter\Filter;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Enum;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Encryption\Security\JWT\Validator;

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
     * Returns the Validator for the token that was sent to us, carrying the
     * values this record and the environment expect that token to hold.
     *
     * @param Token $token The token from the request - it is the one validated
     *
     * @return Validator
     * @throws ModelException
     */
    public function getValidationData(Token $token): Validator
    {
        $validator = new Validator($token, 10);

        return $validator
            ->set(Enum::AUDIENCE, $this->getTokenAudience())
            ->set(Enum::ISSUER, $this->get('issuer'))
            ->set(Enum::ID, $this->get('tokenId'))
        ;
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

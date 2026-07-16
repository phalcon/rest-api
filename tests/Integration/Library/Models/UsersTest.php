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

namespace Phalcon\Api\Tests\Integration\Library\Models;

use Phalcon\Api\Models\Users;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Enum;
use Phalcon\Encryption\Security\JWT\Validator;
use Phalcon\Filter\Filter;

use function count;
use function time;

final class UsersTest extends AbstractIntegrationTestCase
{
    use TokenTrait;

    public function testCheckValidationData(): void
    {
        /** @var Users $user */
        $user = $this->haveRecordWithFields(
            Users::class,
            [
                'username'      => Data::$testUsername,
                'password'      => Data::$testPassword,
                'status'        => 1,
                'issuer'        => 'https://niden.net',
                'tokenPassword' => Data::$strongPassphrase,
                'tokenId'       => Data::$testTokenId,
            ]
        );

        $signer  = new Hmac();
        $builder = new Builder($signer);
        $token   = $builder
            ->setIssuer('https://niden.net')
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setExpirationTime(time() + 10)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $class  = Validator::class;
        $actual = $user->getValidationData($token);
        $this->assertInstanceOf($class, $actual);
    }

    /**
     * Guards the field whitelist at the model, where it is declared. The api
     * suite asserts JSON subsets, so it cannot prove absence on its own.
     */
    public function testGetPublicFields(): void
    {
        $expected = [
            'id',
            'status',
            'username',
            'issuer',
            'tokenId',
        ];

        $actual = (new Users())->getPublicFields();

        $this->assertSame($expected, $actual);
        $this->assertNotContains('password', $actual);
        $this->assertNotContains('tokenPassword', $actual);
    }

    /**
     * getToken() builds and signs the token from the record, which is what the
     * login endpoint hands back.
     */
    public function testGetToken(): void
    {
        /** @var Users $user */
        $user = $this->haveRecordWithFields(
            Users::class,
            [
                'status'        => 1,
                'username'      => Data::$testUsername,
                'password'      => Data::$testPasswordHash,
                'issuer'        => Data::$testIssuer,
                'tokenPassword' => Data::$strongPassphrase,
                'tokenId'       => Data::$testTokenId,
            ]
        );

        $token  = $user->getToken();
        $parsed = $this->getToken($token);
        $claims = $parsed->getClaims();

        $this->assertSame(Data::$testIssuer, $claims->get(Enum::ISSUER));
        $this->assertSame(Data::$testTokenId, $claims->get(Enum::ID));
        $this->assertSame([$this->getTokenAudience()], $claims->get(Enum::AUDIENCE));

        /**
         * The signature must verify against the record's passphrase - that is
         * the whole point of the token.
         *
         * Asserted against get('tokenPassword') rather than the raw constant:
         * get() sanitises, so a passphrase containing '&' comes back as
         * '&amp;'. Signing and verification both read through get(), so the key
         * in use is the sanitised form, not the string handed to set().
         */
        $passphrase = $user->get('tokenPassword');

        $this->assertTrue($parsed->verify(new Hmac(), $passphrase));
        $this->assertFalse($parsed->verify(new Hmac(), 'not-the-passphrase'));

        /**
         * Issued in the past, expiring in the future.
         */
        $now = time();
        $this->assertLessThanOrEqual($now, $claims->get(Enum::ISSUED_AT));
        $this->assertLessThanOrEqual($now, $claims->get(Enum::NOT_BEFORE));
        $this->assertGreaterThan($now, $claims->get(Enum::EXPIRATION_TIME));
    }

    public function testValidateFilters(): void
    {
        $model    = new Users();
        $expected = [
            'id'            => Filter::FILTER_ABSINT,
            'status'        => Filter::FILTER_ABSINT,
            'username'      => Filter::FILTER_STRING,
            'password'      => Filter::FILTER_STRING,
            'issuer'        => Filter::FILTER_STRING,
            'tokenPassword' => Filter::FILTER_STRING,
            'tokenId'       => Filter::FILTER_STRING,
        ];
        $this->assertSame($expected, $model->getModelFilters());
    }

    public function testValidateModel(): void
    {
        $this->haveModelDefinition(
            Users::class,
            [
                'id',
                'status',
                'username',
                'password',
                'issuer',
                'tokenPassword',
                'tokenId',
            ]
        );
    }

    public function testValidateRelationships(): void
    {
        $actual = $this->getModelRelationships(Users::class);
        $this->assertSame(0, count($actual));
    }
}

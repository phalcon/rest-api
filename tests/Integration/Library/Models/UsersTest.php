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
use Phalcon\Encryption\Security\JWT\Validator;
use Phalcon\Filter\Filter;

use function count;
use function time;

final class UsersTest extends AbstractIntegrationTestCase
{
    use TokenTrait;

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

    public function testValidateRelationships(): void
    {
        $actual = $this->getModelRelationships(Users::class);
        $this->assertSame(0, count($actual));
    }
}

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

namespace Phalcon\Api\Tests\Integration\Library\Repositories;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Repositories\UsersRepository;
use Phalcon\Api\Services\QueryService;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Encryption\Security;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;

final class UsersRepositoryTest extends AbstractIntegrationTestCase
{
    use TokenTrait;

    public function testGetByUsernameAndPassword(): void
    {
        $this->haveRecordWithFields(
            Users::class,
            [
                'username' => Data::$testUsername,
                'password' => Data::$testPasswordHash,
                'status'   => 1,
                'issuer'   => 'phalcon.io',
                'tokenId'  => Data::$testTokenId,
            ]
        );

        $dbUser = $this->getRepository()->getByUsernameAndPassword(
            Data::$testUsername,
            Data::$testPassword
        );

        $this->assertNotNull($dbUser);
    }

    /**
     * @throws ModelException
     */
    public function testGetByWrongTokenReturnsNull(): void
    {
        $this->haveRecordWithFields(
            Users::class,
            [
                'username' => Data::$testUsername,
                'password' => Data::$testPasswordHash,
                'status'   => 1,
                'issuer'   => 'phalcon.io',
                'tokenId'  => Data::$testTokenId,
            ]
        );

        $signer  = new Hmac();
        $builder = new Builder($signer);
        $token   = $builder
            ->setIssuer('https://somedomain.com')
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $actual = $this->getRepository()->getByToken($token);

        $this->assertNull($actual);
    }

    public function testGetByWrongUsernameAndPasswordReturnsNull(): void
    {
        $this->haveRecordWithFields(
            Users::class,
            [
                'username' => Data::$testUsername,
                'password' => Data::$testPasswordHash,
                'status'   => 1,
                'issuer'   => 'phalcon.io',
                'tokenId'  => Data::$testTokenId,
            ]
        );

        $dbUser = $this->getRepository()->getByUsernameAndPassword(
            Data::$testUsername,
            'nothing'
        );

        $this->assertNull($dbUser);
    }

    private function getRepository(): UsersRepository
    {
        /** @var Cache $cache */
        $cache = $this->grabFromDi('cache');
        /** @var Config $config */
        $config = $this->grabFromDi('config');
        /** @var Security $security */
        $security = $this->grabFromDi('security');

        return new UsersRepository(new QueryService($config, $cache), $security);
    }
}

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

namespace Phalcon\Api\Tests\Integration\Library\Traits;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;

use function count;
use function Phalcon\Api\Core\appPath;
use function uniqid;

final class QueryTest extends AbstractIntegrationTestCase
{
    use TokenTrait;
    use QueryTrait;

    public function testGetUserByUsernameAndPassword(): void
    {
        /** @var Users $result */
        $this->haveRecordWithFields(
            Users::class,
            [
                'username' => Data::$testUsername,
                'password' => Data::$testPassword,
                'status'   => 1,
                'issuer'   => 'phalcon.io',
                'tokenId'  => Data::$testTokenId,
            ]
        );

        /** @var Cache $cache */
        $cache = $this->grabFromDi('cache');
        /** @var Config $config */
        $config = $this->grabFromDi('config');
        $dbUser = $this->getUserByUsernameAndPassword(
            $config,
            $cache,
            Data::$testUsername,
            Data::$testPassword
        );

        $this->assertNotNull($dbUser);
    }

    public function testGetUserByWrongUsernameAndPasswordReturnsNull(): void
    {
        /** @var Users $result */
        $this->haveRecordWithFields(
            Users::class,
            [
                'username' => Data::$testUsername,
                'password' => Data::$testPassword,
                'status'   => 1,
                'issuer'   => 'phalcon.io',
                'tokenId'  => Data::$testTokenId,
            ]
        );

        /** @var Cache $cache */
        $cache = $this->grabFromDi('cache');
        /** @var Config $config */
        $config = $this->grabFromDi('config');
        $dbUser = $this->getUserByUsernameAndPassword(
            $config,
            $cache,
            Data::$testUsername,
            'nothing'
        );

        $this->assertNull($dbUser);
    }

    /**
     * @throws ModelException
     */
    public function testGetUserByWrongTokenReturnsNull(): void
    {
        /** @var Users $result */
        $this->haveRecordWithFields(
            Users::class,
            [
                'username' => Data::$testUsername,
                'password' => Data::$testPassword,
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

        /** @var Cache $cache */
        $cache = $this->grabFromDi('cache');
        /** @var Config $config */
        $config = $this->grabFromDi('config');
        $actual = $this->getUserByToken($config, $cache, $token);

        $this->assertNull($actual);
    }

    public function testGetCompaniesCachedData(): void
    {
        $configData = require appPath('./library/Core/config.php');
        $this->assertTrue($configData['app']['devMode']);

        $configData['app']['devMode'] = false;
        /** @var Config $config */
        $config    = new Config($configData);
        $container = $this->grabDi();
        $container->set('config', $config);
        $this->assertFalse($config->path('app.devMode'));

        /** @var Cache $cache */
        $cache = $this->grabFromDi('cache');
        $cache->clear();
        /** @var Config $config */
        $config = $this->grabFromDi('config');
        $this->assertFalse($config->path('app.devMode'));

        /**
         * Company 1
         */
        $comName = uniqid('com-cached-');
        $comOne  = $this->haveRecordWithFields(
            Companies::class,
            [
                'name'    => $comName,
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );

        $results = $this->getRecords($config, $cache, Companies::class);
        $this->assertSame(1, count($results));
        $this->assertSame($comName, $results[0]->get('name'));
        $this->assertSame($comOne->get('address'), $results[0]->get('address'));
        $this->assertSame($comOne->get('city'), $results[0]->get('city'));
        $this->assertSame($comOne->get('phone'), $results[0]->get('phone'));

        /**
         * Get the record again but ensure the name has been changed
         */
        $result = $comOne->set('name', 'com-cached-change')
                         ->save()
        ;
        $this->assertNotEquals(false, $result);

        /**
         * This should return the cached result
         */
        $results = $this->getRecords($config, $cache, Companies::class);
        $this->assertSame(1, count($results));
        $this->assertSame($comName, $results[0]->get('name'));
        $this->assertSame($comOne->get('address'), $results[0]->get('address'));
        $this->assertSame($comOne->get('city'), $results[0]->get('city'));
        $this->assertSame($comOne->get('phone'), $results[0]->get('phone'));
    }
}

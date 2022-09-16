<?php

namespace Phalcon\Api\Tests\integration\library\Traits;

use IntegrationTester;
use Page\Data;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;

use function Phalcon\Api\Core\appPath;

class QueryCest
{
    use TokenTrait;
    use QueryTrait;

    /**
     * @param IntegrationTester $I
     *
     */
    public function checkGetUserByUsernameAndPassword(IntegrationTester $I)
    {
        /** @var Users $result */
        $I->haveRecordWithFields(
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
        $cache = $I->grabFromDi('cache');
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $dbUser = $this->getUserByUsernameAndPassword(
            $config,
            $cache,
            Data::$testUsername,
            Data::$testPassword
        );

        $I->assertNotNull($dbUser);
    }

    /**
     * @param IntegrationTester $I
     *
     */
    public function checkGetUserByWrongUsernameAndPasswordReturnsNull(IntegrationTester $I)
    {
        /** @var Users $result */
        $I->haveRecordWithFields(
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
        $cache = $I->grabFromDi('cache');
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $dbUser = $this->getUserByUsernameAndPassword(
            $config,
            $cache,
            Data::$testUsername,
            'nothing'
        );

        $I->assertNull($dbUser);
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function checkGetUserByWrongTokenReturnsNull(IntegrationTester $I)
    {
        /** @var Users $result */
        $I->haveRecordWithFields(
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
        $cache = $I->grabFromDi('cache');
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $actual = $this->getUserByToken($config, $cache, $token);

        $I->assertNull($actual);
    }

    public function getCompaniesCachedData(IntegrationTester $I)
    {
        $configData = require appPath('./library/Core/config.php');
        $I->assertTrue($configData['app']['devMode']);

        $configData['app']['devMode'] = false;
        /** @var Config $config */
        $config    = new Config($configData);
        $container = $I->grabDi();
        $container->set('config', $config);
        $I->assertFalse($config->path('app.devMode'));

        /** @var Cache $cache */
        $cache = $I->grabFromDi('cache');
        $cache->clear();
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $I->assertFalse($config->path('app.devMode'));

        /**
         * Company 1
         */
        $comName = uniqid('com-cached-');
        $comOne  = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => $comName,
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );

        $results = $this->getRecords($config, $cache, Companies::class);
        $I->assertSame(1, count($results));
        $I->assertSame($comName, $results[0]->get('name'));
        $I->assertSame($comOne->get('address'), $results[0]->get('address'));
        $I->assertSame($comOne->get('city'), $results[0]->get('city'));
        $I->assertSame($comOne->get('phone'), $results[0]->get('phone'));

        /**
         * Get the record again but ensure the name has been changed
         */
        $result = $comOne->set('name', 'com-cached-change')
                         ->save()
        ;
        $I->assertNotEquals(false, $result);

        /**
         * This should return the cached result
         */
        $results = $this->getRecords($config, $cache, Companies::class);
        $I->assertSame(1, count($results));
        $I->assertSame($comName, $results[0]->get('name'));
        $I->assertSame($comOne->get('address'), $results[0]->get('address'));
        $I->assertSame($comOne->get('city'), $results[0]->get('city'));
        $I->assertSame($comOne->get('phone'), $results[0]->get('phone'));
    }
}

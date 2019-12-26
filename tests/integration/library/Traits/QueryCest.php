<?php

namespace Phalcon\Api\Tests\integration\library\Traits;

use IntegrationTester;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Cache;
use Phalcon\Config;
use function Phalcon\Api\Core\appPath;

/**
 * Class QueryCest
 */
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
                'username' => 'testusername',
                'password' => 'testpass',
                'status'   => 1,
                'issuer'   => 'phalcon.io',
                'tokenId'  => '00110011',
            ]
        );

        /** @var Cache $cache */
        $cache  = $I->grabFromDi('cache');
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $dbUser = $this->getUserByUsernameAndPassword($config, $cache, 'testusername', 'testpass');

        $I->assertNotEquals(false, $dbUser);
    }

    /**
     * @param IntegrationTester $I
     *
     */
    public function checkGetUserByWrongUsernameAndPasswordReturnsFalse(IntegrationTester $I)
    {
        /** @var Users $result */
        $I->haveRecordWithFields(
            Users::class,
            [
                'username' => 'testusername',
                'password' => 'testpass',
                'status'   => 1,
                'issuer'   => 'phalcon.io',
                'tokenId'  => '00110011',
            ]
        );

        /** @var Cache $cache */
        $cache  = $I->grabFromDi('cache');
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $I->assertFalse($this->getUserByUsernameAndPassword($config, $cache, 'testusername', 'nothing'));
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function checkGetUserByWrongTokenReturnsFalse(IntegrationTester $I)
    {
        /** @var Users $result */
        $I->haveRecordWithFields(
            Users::class,
            [
                'username' => 'testusername',
                'password' => 'testpass',
                'status'   => 1,
                'issuer'   => 'phalcon.io',
                'tokenId'  => '00110011',
            ]
        );

        $signer  = new Sha512();
        $builder = new Builder();
        $token   = $builder
            ->setIssuer('https://somedomain.com')
            ->setAudience($this->getTokenAudience())
            ->setId('123456', true)
            ->sign($signer, '110011')
            ->getToken()
        ;

        /** @var Cache $cache */
        $cache  = $I->grabFromDi('cache');
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $I->assertFalse($this->getUserByToken($config, $cache, $token));
    }

    public function getCompaniesCachedData(IntegrationTester $I)
    {
        $configData = require appPath('./library/Core/config.php');
        $I->assertTrue($configData['app']['devMode']);

        $configData['app']['devMode'] = false;
        /** @var Config $config */
        $config = new Config($configData);
        $container = $I->grabDi();
        $container->set('config', $config);
        $I->assertFalse($config->path('app.devMode'));

        /** @var Cache $cache */
        $cache  = $I->grabFromDi('cache');
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
        $I->assertEquals(1, count($results));
        $I->assertEquals($comName, $results[0]->get('name'));
        $I->assertEquals($comOne->get('address'), $results[0]->get('address'));
        $I->assertEquals($comOne->get('city'), $results[0]->get('city'));
        $I->assertEquals($comOne->get('phone'), $results[0]->get('phone'));

        /**
         * Get the record again but ensure the name has been changed
         */
        $result = $comOne->set('name', 'com-cached-change')->save();
        $I->assertNotEquals(false, $result);

        /**
         * This should return the cached result
         */
        $results = $this->getRecords($config, $cache, Companies::class);
        $I->assertEquals(1, count($results));
        $I->assertEquals($comName, $results[0]->get('name'));
        $I->assertEquals($comOne->get('address'), $results[0]->get('address'));
        $I->assertEquals($comOne->get('city'), $results[0]->get('city'));
        $I->assertEquals($comOne->get('phone'), $results[0]->get('phone'));
    }
}

<?php

namespace Niden\Tests\integration\library\Traits;

use IntegrationTester;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Niden\Exception\ModelException;
use Niden\Models\Users;
use Niden\Traits\QueryTrait;
use Niden\Traits\TokenTrait;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Config;

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
     * @throws ModelException
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
                'issuer'   => 'phalconphp.com',
                'tokenId'  => '00110011',
            ]
        );

        /** @var Libmemcached $cache */
        $cache  = $I->grabFromDi('cache');
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $dbUser = $this->getUserByUsernameAndPassword($config, $cache, 'testusername', 'testpass');

        $I->assertNotEquals(false, $dbUser);
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
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
                'issuer'   => 'phalconphp.com',
                'tokenId'  => '00110011',
            ]
        );

        /** @var Libmemcached $cache */
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
                'issuer'   => 'phalconphp.com',
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

        /** @var Libmemcached $cache */
        $cache  = $I->grabFromDi('cache');
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $I->assertFalse($this->getUserByToken($config, $cache, $token));
    }
}

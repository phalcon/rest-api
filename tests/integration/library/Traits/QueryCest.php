<?php

namespace Niden\Tests\integration\library\Traits;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use \IntegrationTester;
use Niden\Models\Users;
use Niden\Exception\ModelException;
use Niden\Traits\QueryTrait;
use Niden\Traits\TokenTrait;

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

        $dbUser = $this->getUserByUsernameAndPassword('testusername', 'testpass');

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

        $I->assertFalse($this->getUserByUsernameAndPassword('testusername', 'nothing'));
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
            ->getToken();

        $I->assertFalse($this->getUserByToken($token));
    }
}

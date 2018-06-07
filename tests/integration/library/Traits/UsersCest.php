<?php

namespace Niden\Tests\integration\library\Traits;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use \IntegrationTester;
use Niden\Models\Users;
use Niden\Exception\ModelException;
use Niden\Traits\TokenTrait;
use Niden\Traits\UserTrait;

/**
 * Class ModelCest
 */
class UsersCest
{
    use TokenTrait;
    use UserTrait;

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
                'usr_id'          => 1000,
                'usr_username'    => 'testusername',
                'usr_password'    => 'testpass',
                'usr_status_flag' => 1,
                'usr_domain_name' => 'phalconphp.com',
                'usr_token_id'    => '00110011',

            ]
        );

        $dbUser = $this->getUserByUsernameAndPassword('testusername', 'testpass');

        $I->assertEquals(1000, $dbUser->get('usr_id'));
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
                'usr_id'          => 1001,
                'usr_username'    => 'testusername',
                'usr_password'    => 'testpass',
                'usr_status_flag' => 1,
                'usr_domain_name' => 'phalconphp.com',
                'usr_token_id'    => '00110011',

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
                'usr_id'          => 1003,
                'usr_username'    => 'testusername',
                'usr_password'    => 'testpass',
                'usr_status_flag' => 1,
                'usr_domain_name' => 'phalconphp.com',
                'usr_token_id'    => '00110011',

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

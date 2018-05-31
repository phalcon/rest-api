<?php

namespace Niden\Tests\integration;

use function Niden\Functions\appPath;
use Codeception\Stub;
use \IntegrationTester;
use Niden\Logger;
use Niden\Models\Users;
use Niden\Exception\ModelException;
use Niden\Traits\UserTrait;
use Phalcon\Mvc\Model\Message;

/**
 * Class ModelCest
 */
class UsersCest
{
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
                'usr_token_pre'   => '123',
                'usr_token_mid'   => '456',
                'usr_token_post'  => '789',
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
    public function checkGetUserByUsernameAndPasswordThrowsException(IntegrationTester $I)
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
                'usr_token_pre'   => '123',
                'usr_token_mid'   => '456',
                'usr_token_post'  => '789',
                'usr_token_id'    => '00110011',

            ]
        );

        $I->expectException(
            ModelException::class,
            function () {
                $this->getUserByUsernameAndPassword('testusername', 'nothing');
            }
        );
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function checkGetUserByToken(IntegrationTester $I)
    {
        /** @var Users $result */
        $I->haveRecordWithFields(
            Users::class,
            [
                'usr_id'          => 1002,
                'usr_username'    => 'testusername',
                'usr_password'    => 'testpass',
                'usr_status_flag' => 1,
                'usr_domain_name' => 'phalconphp.com',
                'usr_token_pre'   => '123',
                'usr_token_mid'   => '456',
                'usr_token_post'  => '789',
                'usr_token_id'    => '00110011',

            ]
        );

        $dbUser = $this->getUserByToken('123.456.789');

        $I->assertEquals(1002, $dbUser->get('usr_id'));
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function checkGetUserByTokenThrowsException(IntegrationTester $I)
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
                'usr_token_pre'   => '123',
                'usr_token_mid'   => '456',
                'usr_token_post'  => '789',
                'usr_token_id'    => '00110011',

            ]
        );

        $I->expectException(
            ModelException::class,
            function () {
                $this->getUserByToken('sometoken');
            }
        );
    }
}

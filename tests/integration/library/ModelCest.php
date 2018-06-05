<?php

namespace Niden\Tests\integration;

use function Niden\Core\appPath;
use Codeception\Stub;
use \IntegrationTester;
use Niden\Logger;
use Niden\Models\Users;
use Niden\Exception\ModelException;
use Phalcon\Mvc\Model\Message;

/**
 * Class ModelCest
 */
class ModelCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelGetTablePrefix(IntegrationTester $I)
    {
        /** @var Users $result */
        $user = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_id'             => 1000,
                'usr_username'       => 'testusername',
                'usr_password'       => 'testpass',
                'usr_status_flag'    => 1,
                'usr_domain_name'    => 'phalconphp.com',
                'usr_token_password' => '12345',
                'usr_token_pre'      => '123',
                'usr_token_mid'      => '456',
                'usr_token_post'     => '789',
                'usr_token_id'       => '00110011',

            ]
        );

        $I->assertEquals('usr', $user->getTablePrefix());
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelGetSetFields(IntegrationTester $I)
    {
        $user = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_id'             => 1000,
                'usr_username'       => 'testusername',
                'usr_password'       => 'testpass',
                'usr_status_flag'    => 1,
                'usr_domain_name'    => 'phalconphp.com',
                'usr_token_password' => '12345',
                'usr_token_pre'      => '123',
                'usr_token_mid'      => '456',
                'usr_token_post'     => '789',
                'usr_token_id'       => '00110011',
            ]
        );

        $I->assertEquals(1000, $user->get('usr_id'));
    }

    /**
     * Tests the model by setting a non existent field
     *
     * @param IntegrationTester $I
     */
    public function modelSetNonExistingFields(IntegrationTester $I)
    {
        $I->expectException(
            ModelException::class,
            function () {
                $fixture = new Users();
                $fixture->set('usr_id', 1000)
                        ->set('some_field', true)
                        ->save()
                ;
            }
        );
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelGetNonExistingFields(IntegrationTester $I)
    {
        /** @var Users $result */
        $user = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_id'             => 1000,
                'usr_username'       => 'testusername',
                'usr_password'       => 'testpass',
                'usr_status_flag'    => 1,
                'usr_domain_name'    => 'phalconphp.com',
                'usr_token_password' => '12345',
                'usr_token_pre'      => '123',
                'usr_token_mid'      => '456',
                'usr_token_post'     => '789',
                'usr_token_id'       => '00110011',

            ]
        );

        $I->expectException(
            ModelException::class,
            function () use ($user) {
                $user->get('some_field');
            }
        );
    }

    /**
     * Tests the model update interactions
     *
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelUpdateFields(IntegrationTester $I)
    {
        /** @var Users $result */
        $user = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_id'             => 1000,
                'usr_username'       => 'testusername',
                'usr_password'       => 'testpass',
                'usr_status_flag'    => 1,
                'usr_domain_name'    => 'phalconphp.com',
                'usr_token_password' => '12345',
                'usr_token_pre'      => '123',
                'usr_token_mid'      => '456',
                'usr_token_post'     => '789',
                'usr_token_id'       => '00110011',

            ]
        );

        $user->set('usr_username', 'testusername')
             ->save()
        ;

        $I->assertEquals($user->get('usr_username'), 'testusername');
        $I->assertEquals($user->get('usr_password'), 'testpass');
        $I->assertEquals($user->get('usr_domain_name'), 'phalconphp.com');
        $I->assertEquals($user->get('usr_token_password'), '12345');
        $I->assertEquals($user->get('usr_token_pre'), '123');
        $I->assertEquals($user->get('usr_token_mid'), '456');
        $I->assertEquals($user->get('usr_token_post'), '789');
        $I->assertEquals($user->get('usr_token_id'), '00110011');
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelUpdateFieldsNotSanitized(IntegrationTester $I)
    {
        /** @var Users $result */
        $user = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_id'             => 1000,
                'usr_username'       => 'testusername',
                'usr_password'       => 'testpass',
                'usr_status_flag'    => 1,
                'usr_domain_name'    => 'phalconphp.com',
                'usr_token_password' => '12345',
                'usr_token_pre'      => '123',
                'usr_token_mid'      => '456',
                'usr_token_post'     => '789',
                'usr_token_id'       => '00110011',

            ]
        );

        $user->set('usr_password', 'abcde\nfg')
             ->save()
        ;
        $I->assertEquals($user->get('usr_password'), 'abcde\nfg');

        /** Not sanitized */
        $user->set('usr_password', 'abcde\nfg', false)
             ->save()
        ;
        $I->assertEquals($user->get('usr_password'), 'abcde\nfg');
    }

    /**
     * @param IntegrationTester $I
     */
    public function checkModelMessages(IntegrationTester $I)
    {
        $user = Stub::construct(
            Users::class,
            [],
            [
                'save'        => false,
                'getMessages' => [
                    new Message('error 1'),
                    new Message('error 2'),
                ],
            ]
        );

        $result = $user
            ->set('usr_username', 'test')
            ->save();
        $I->assertFalse($result);

        $I->assertEquals('error 1<br />error 2<br />', $user->getModelMessages());
    }

    /**
     * @param IntegrationTester $I
     */
    public function checkModelMessagesWithLogger(IntegrationTester $I)
    {
        /** @var Logger $logger */
        $logger = $I->grabFromDi('logger');
        $user   = Stub::construct(
            Users::class,
            [],
            [
                'save'        => false,
                'getMessages' => [
                    new Message('error 1'),
                    new Message('error 2'),
                ],
            ]
        );

        $fileName = appPath('storage/logs/api.log');
        $result   = $user
            ->set('usr_username', 'test')
            ->save();
        $I->assertFalse($result);
        $I->assertEquals('error 1<br />error 2<br />', $user->getModelMessages());

        $user->getModelMessages($logger);

        $I->openFile($fileName);
        $I->seeInThisFile("error 1\n");
        $I->seeInThisFile("error 2\n");
    }
}

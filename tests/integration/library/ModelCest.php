<?php

namespace Phalcon\Api\Tests\integration\library;

use Codeception\Stub;
use Exception;
use IntegrationTester;
use Monolog\Logger;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Users;
use Phalcon\Messages\Message;
use function Phalcon\Api\Core\appPath;

/**
 * Class ModelCest
 */
class ModelCest
{
    /**
     * @param IntegrationTester $I
     */
    public function modelGetSetFields(IntegrationTester $I)
    {
        $I->haveRecordWithFields(
            Users::class,
            [
                'username'      => 'testusername',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'phalcon.io',
                'tokenPassword' => '12345',
                'tokenId'       => '00110011',
            ]
        );
    }

    /**
     * Tests the model by setting a non existent field
     *
     * @param IntegrationTester $I
     */
    public function modelSetNonExistingFields(IntegrationTester $I)
    {
        $I->expectThrowable(
            ModelException::class,
            function () {
                $fixture = new Users();
                $fixture->set('id', 1000)
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
                'username'      => 'testusername',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'phalcon.io',
                'tokenPassword' => '12345',
                'tokenId'       => '00110011',
            ]
        );

        $I->expectThrowable(
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
                'username'      => 'testusername',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'phalcon.io',
                'tokenPassword' => '12345',
                'tokenId'       => '00110011',
            ]
        );

        $user->set('username', 'testusername')
             ->save()
        ;

        $I->assertEquals($user->get('username'), 'testusername');
        $I->assertEquals($user->get('password'), 'testpass');
        $I->assertEquals($user->get('issuer'), 'phalcon.io');
        $I->assertEquals($user->get('tokenPassword'), '12345');
        $I->assertEquals($user->get('tokenId'), '00110011');
    }

    /**
     * @param IntegrationTester $I
     */
    public function modelUpdateFieldsNotSanitized(IntegrationTester $I)
    {
        /** @var Users $result */
        $user = $I->haveRecordWithFields(
            Users::class,
            [
                'username'      => 'testusername',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'phalcon.io',
                'tokenPassword' => '12345',
                'tokenId'       => '00110011',

            ]
        );

        $user->set('password', 'abcde\nfg')
             ->save()
        ;
        $I->assertEquals($user->get('password'), 'abcde\nfg');

        /** Not sanitized */
        $user->set('password', 'abcde\nfg', false)
             ->save()
        ;
        $I->assertEquals($user->get('password'), 'abcde\nfg');
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
            ->set('username', 'test')
            ->save()
        ;
        $I->assertFalse($result);

        $I->assertEquals('error 1<br />error 2<br />', $user->getModelMessages());
    }

    /**
     * @param IntegrationTester $I
     * @throws Exception
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
            ->set('username', 'test')
            ->save()
        ;
        $I->assertFalse($result);
        $I->assertEquals('error 1<br />error 2<br />', $user->getModelMessages());

        $user->getModelMessages($logger);

        $I->openFile($fileName);
        $I->seeInThisFile("error 1\n");
        $I->seeInThisFile("error 2\n");
    }
}

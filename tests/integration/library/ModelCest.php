<?php

namespace Niden\Tests\integration;

use function Niden\Functions\appPath;
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
        $user = $this->createFixture($I, 1000);

        $I->assertEquals('usr', $user->getTablePrefix());
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelGetSetFields(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $this->createFixture($I, 1000);

        $this->checkFields($I, $user, 1000);
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
                $fixture->set('usr_id', $I, 1000)
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
        /** @var Users $user */
        $user = $this->createFixture($I, 1000);
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
        /** @var Users $user */
        $user = $this->createFixture($I, 1000);

        $user->set('usr_username', 'testusername')
             ->save()
        ;

        $I->assertEquals($user->get('usr_username'), 'testusername');
        $I->assertEquals($user->get('usr_password'), 'testpass');
        $I->assertEquals($user->get('usr_domain_name'), 'phalconphp.com');
        $I->assertEquals($user->get('usr_token'), '12345');
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelUpdateFieldsNotSanitized(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $this->createFixture($I, 1000);
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

    /**
     * Creates a new record in the database
     *
     * @param IntegrationTester $I
     * @param int               $userId
     *
     * @return Users
     * @throws ModelException
     */
    private function createFixture(IntegrationTester $I, int $userId)
    {
        /**
         * Just in case delete any records that might be already in there
         */
        $fixture = Users::findFirst(
            [
                'conditions' => 'usr_id = :usr_id:',
                'bind'       => ['usr_id' => $userId]]
        );
        if (false !== $fixture) {
            $fixture->delete();
        }

        $fixture = new Users();
        $result  = $fixture
            ->set('usr_id', $userId)
            ->set('usr_username', 'testusername')
            ->set('usr_password', 'testpass')
            ->set('usr_status_flag', 1)
            ->set('usr_domain_name', 'phalconphp.com')
            ->set('usr_token', '12345')
            ->save()
        ;

        $I->assertNotSame(false, $result);

        return $fixture;
    }

    /**
     * Asserts whether the fields of a model are the same as what we expect
     *
     * @param IntegrationTester $I
     * @param Users             $user
     * @param int               $userId
     *
     * @throws \Niden\Exception\ModelException
     */
    private function checkFields(IntegrationTester $I, Users $user, int $userId)
    {
        $I->assertEquals($user->get('usr_id'), $userId);
        $I->assertEquals($user->get('usr_username'), 'testusername');
        $I->assertEquals($user->get('usr_password'), 'testpass');
        $I->assertEquals($user->get('usr_status_flag'), 1);
    }
}

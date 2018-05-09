<?php

namespace Niden\Tests\integration;

use \IntegrationTester;
use Niden\Models\Users;
use Niden\Exception\ModelException;

/**
 * Class ModelCest
 */
class ModelCest
{
    protected $user;

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelGetSetFields(IntegrationTester $I)
    {
        $result = $this->createFixture(1000);

        $I->assertNotSame(false, $result);

        $I->amGoingTo('get the record from the database again and testing data');
        /** @var Users $user */
        $user = $this->getFixture(1000);

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
        $result = $this->createFixture(1000);

        $I->assertNotSame(false, $result);
        /** @var Users $user */
        $user = $this->getFixture(1000);
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
        $result = $this->createFixture(1000);

        $I->assertNotSame(false, $result);

        /** @var Users $user */
        $user = $this->getFixture(1000);

        $user->set('usr_username', 'testusername')
             ->save()
        ;

        $user = $this->getFixture(1000);

        $I->assertEquals($user->get('usr_username'), 'testusername');
        $I->assertEquals($user->get('usr_password'), 'testpass');
    }

    /**
     * Creates a new record in the database
     *
     * @param int $userId
     *
     * @return bool
     * @throws ModelException
     */
    private function createFixture(int $userId)
    {
        /**
         * Just in case delete any records that might be already in there
         */
        $fixture = $this->getFixture($userId);
        if (false !== $fixture) {
            $fixture->delete();
        }

        $fixture = new Users();
        $result  = $fixture
            ->set('usr_id', $userId)
            ->set('usr_username', 'testusername')
            ->set('usr_password', 'testpass')
            ->set('usr_status_flag', 1)
            ->save()
        ;

        return $result;
    }

    /**
     * Gets a record from the database
     *
     * @param int $userId
     *
     * @return Users|false
     */
    private function getFixture(int $userId)
    {
        return $this->user = Users::findFirst(
            [
                'conditions' => 'usr_id = :usr_id:',
                'bind'       => ['usr_id' => $userId],
            ]
        );
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


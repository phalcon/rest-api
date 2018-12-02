<?php

namespace Gewaer\Tests\api;

use ApiTester;
use Page\Data;
use function json_decode;
use Exception;
use Phalcon\Security\Random;

class AuthCest
{
    /**
     * Test login error
     *
     * @param ApiTester $I
     * @return void
     */
    public function loginUnknownUser(ApiTester $I)
    {
        try {
            $I->sendPOST(
                Data::$loginUrl,
                [
                    'email' => 'user',
                    'password' => 'pass',
                ]
            );

            $response = $e->getMessage();
        } catch (Exception $e) {
            $response = $e->getMessage();
        }

        $I->assertEquals('No User Found', $response);
    }

    /**
     * Create new users
     *
     * @param ApiTester $I
     * @return void
     */
    public function signup(ApiTester $I)
    {
        $random = new Random();
        $userName = $random->base58();

        $I->sendPOST(Data::$usersUrl, [
            'email' => 'tes2t@baka.io',
            'password' => 'bakatest123567',
            'firstname' => $userName,
            'lastname' => $userName,
            'displayname' => $userName,
            'default_company' => $userName,
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();

        $data = json_decode($response, true);

        $I->assertTrue(isset($data['user']['id']));
        $I->assertTrue(isset($data['user']['email']));
    }

    /**
     * Test login user
     *
     * @param ApiTester $I
     * @return void
     */
    public function loginKnownUser(ApiTester $I)
    {
        $I->sendPOST(Data::$loginUrl, Data::loginJson());

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();

        $data = json_decode($response, true);
        $I->assertTrue(isset($data['id']));
        $I->assertTrue(isset($data['token']));
    }
}

<?php

namespace Gewaer\Tests\api;

use ApiTester;
use Page\Data;
use function json_decode;
use Throwable;

class LoginCest
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
        } catch (Throwable $e) {
            $response = $e->getMessage();
        }

        $I->assertEquals('No User Found', $response);
    }

    /**
     * Test login user
     *
     * @param ApiTester $I
     * @return void
     */
    public function loginKnownUser(ApiTester $I)
    {
        return true;
        try {
            $I->sendPOST(Data::$loginUrl, Data::loginJson());
        } catch (Throwable $e) {
            print_R($e->getFile());
            print_R($e->getMessage());
        }

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);
        $I->assertTrue(isset($data['id']));
        $I->assertTrue(isset($data['token']));
    }
}

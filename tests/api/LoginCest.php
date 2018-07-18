<?php

namespace Niden\Tests\api;

use ApiTester;
use function json_decode;
use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Models\Users;
use Page\Data;

class LoginCest
{
    public function loginUnknownUser(ApiTester $I)
    {
        $I->sendPOST(
            Data::$loginUrl,
            [
                'username' => 'user',
                'password' => 'pass',
            ]
        );
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Incorrect credentials');
    }

    public function loginKnownUser(ApiTester $I)
    {
        $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag' => 1,
                'usr_username'    => 'testuser',
                'usr_password'    => 'testpassword',
                'usr_issuer'      => 'https://phalconphp.com',
                'usr_token_id'    => '110011',
            ]
        );

        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data     = json_decode($response, true);
        $I->assertTrue(isset($data['data']));
        $I->assertTrue(isset($data['data']['token']));
        $I->assertTrue(isset($data['meta']));
    }
}

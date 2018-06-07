<?php

namespace Niden\Tests\api;

use ApiTester;
use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Models\Users;
use Page\Data;

class LoginCest
{
    public function loginNoDataElement(ApiTester $I)
    {
        $I->sendPOST(
            Data::$loginUrl,
            json_encode(
                [
                    'username' => 'user',
                    'password' => 'pass',
                ]
            )
        );
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('"data" element not present in the payload');
    }

    public function loginUnknownUser(ApiTester $I)
    {
        $I->sendPOST(
            Data::$loginUrl,
            json_encode(
                [
                    'data' => [
                        'username' => 'user',
                        'password' => 'pass',
                    ]
                ]
            )
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
                'usr_domain_name' => 'https://phalconphp.com',
                'usr_token_id'    => '110011',
            ]
        );

        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }
}

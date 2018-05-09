<?php

namespace Niden\Tests\api;

use ApiTester;
use Niden\Http\Response;

class LoginCest
{
    public function loginUnknownUser(ApiTester $I)
    {
        $I->sendPOST(
            '/login',
            json_encode(
                [
                    'username' => 'user',
                    'password' => 'pass',
                ]
            )
        );
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'data'   => [],
                'errors' => [
                    'code'   => Response::STATUS_ERROR,
                    'source' => 'Login',
                    'detail' => 'Incorrect credentials',
                ],
            ]
        );
    }
}

<?php

namespace Niden\Tests\api;

use ApiTester;
use Niden\Http\Response;
use Niden\Models\Users;

class LoginCest
{
    public function loginNoDataElement(ApiTester $I)
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
                    'source' => 'beforeExecuteRoute',
                    'detail' => '"data" element not present in the payload',
                ],
            ]
        );
    }

    public function loginUnknownUser(ApiTester $I)
    {
        $I->sendPOST(
            '/login',
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

    public function loginKnownUser(ApiTester $I)
    {
        $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag' => 1,
                'usr_username'    => 'testuser',
                'usr_password'    => 'testpassword',
                'usr_domain_name' => 'phalconphp',
                'usr_token'       => 'abcdef123456',
                'usr_token_id'    => '110011',
            ]
        );

        $I->sendPOST(
            '/login',
            json_encode(
                [
                    'data' => [
                        'username' => 'testuser',
                        'password' => 'testpassword',
                    ]
                ]
            )
        );
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'data'   => [
                    'token' => 'abcdef123456'
                ],
                'errors' => [
                    'code'   => Response::STATUS_SUCCESS,
                    'source' => '',
                    'detail' => '',
                ],
            ]
        );
    }
}

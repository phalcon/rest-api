<?php

namespace Niden\Tests\api;

use ApiTester;
use Niden\Http\Response;
use Niden\Models\Users;

class UserCest
{
//    public function loginKnownUserIncorrectToken(ApiTester $I)
//    {
//        $I->deleteHeader('Authorization');
//        $I->sendPOST(
//            '/user/get',
//            json_encode(
//                [
//                    'data' => [
//                        'userId' => 1,
//                    ]
//                ]
//            )
//        );
//
//        $I->seeResponseIsSuccessful();
//        $I->seeResponseContainsJson(
//            [
//                'jsonapi' => [
//                    'version' => '1.0',
//                ],
//                'data'   => [],
//                'errors' => [
//                    'code'   => Response::STATUS_ERROR,
//                    'source' => 'Auth',
//                    'detail' => 'Authentication Error',
//                ],
//            ]
//        );
//    }

    public function loginKnownUserCorrectToken(ApiTester $I)
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoiYWFhYWFhIn0.eyJpc3MiOiJodHRwczpcL1wvcGhhbGNvbnBocC5jb20iLCJhdWQiOiJodHRwczpcL1wvbmlkZW4ubmV0IiwianRpIjoiYWFhYWFhIiwiaWF0IjoxNTI3MjgyMzYyLCJuYmYiOjE1MjcyODI0MjIsImV4cCI6MTUyNzI4NTk2MiwidWlkIjoxfQ';
        $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag' => 1,
                'usr_username'    => 'testuser',
                'usr_password'    => 'testpassword',
                'usr_domain_name' => 'https://phalconphp.com',
                'usr_token'       => $token,
                'usr_token_id'    => '110011',
            ]
        );

        $I->deleteHeader('Authorization');
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
                    'token' => $token,
                ],
                'errors' => [
                    'code'   => Response::STATUS_SUCCESS,
                    'source' => '',
                    'detail' => '',
                ],
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(
            '/user/get',
            json_encode(
                [
                    'data' => [
                        'userId' => 1,
                    ]
                ]
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'data'   => [
                    'Hello',
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

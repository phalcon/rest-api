<?php

namespace Niden\Tests\api;

use ApiTester;
use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Models\Users;

class UserCest
{
    public function loginKnownUserNoToken(ApiTester $I)
    {
        $I->expectException(
            new Exception('Invalid Token'),
            function () use ($I) {
                $I->deleteHeader('Authorization');
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
            }
        );
    }

    public function loginKnownUserIncorrectSignatureInToken(ApiTester $I)
    {
        $I->expectException(
            new Exception('Invalid Token'),
            function () use ($I) {
                $this->addRecord($I);
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
                        'errors'  => [
                            'code'   => Response::STATUS_SUCCESS,
                            'detail' => '',
                        ],
                    ]
                );

                $record  = $I->getRecordWithFields(Users::class, ['usr_username' => 'testuser']);
                $dbToken = $record->get('usr_token_pre') . '.'
                         . $record->get('usr_token_mid') . '.'
                         . $record->get('usr_token_post');
                $record->set('usr_token_password', '456789')->save();

                $response = $I->grabResponse();
                $response  = json_decode($response, true);
                $data      = $response['data'];
                $token     = $data['token'];
                $I->assertEquals($dbToken, $token);

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
            }
        );
    }

    public function loginKnownUserIncorrectToken(ApiTester $I)
    {
        $I->expectException(
            new Exception('Invalid Token'),
            function () use ($I) {
                $this->addRecord($I);

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
                        'errors'  => [
                            'code'   => Response::STATUS_SUCCESS,
                            'detail' => '',
                        ],
                    ]
                );

                $I->haveHttpHeader('Authorization', 'Bearer abcde');
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
            }
        );
    }

    public function loginKnownUserCorrectToken(ApiTester $I)
    {
        $this->addRecord($I);

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

        $record  = $I->getRecordWithFields(Users::class, ['usr_username' => 'testuser']);
        $dbToken = $record->get('usr_token_pre') . '.'
                 . $record->get('usr_token_mid') . '.'
                 . $record->get('usr_token_post');

        $response = $I->grabResponse();
        $response  = json_decode($response, true);
        $data      = $response['data'];
        $token     = $data['token'];
        $I->assertEquals($dbToken, $token);
    }

    public function loginKnownUserValidToken(ApiTester $I)
    {
        $user = $this->addRecord($I);

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

        $record  = $I->getRecordWithFields(Users::class, ['usr_username' => 'testuser']);
        $dbToken = $record->get('usr_token_pre') . '.'
                 . $record->get('usr_token_mid') . '.'
                 . $record->get('usr_token_post');

        $response = $I->grabResponse();
        $response  = json_decode($response, true);
        $data      = $response['data'];
        $token     = $data['token'];
        $I->assertEquals($dbToken, $token);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(
            '/user/get',
            json_encode(
                [
                    'data' => [
                        'userId' => $user->get('usr_id'),
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
                    [
                        'id'            => $user->get('usr_id'),
                        'status'        => $user->get('usr_status_flag'),
                        'username'      => $user->get('usr_username'),
                        'domainName'    => $user->get('usr_domain_name'),
                        'tokenPassword' => $user->get('usr_token_password'),
                        'tokenId'       => $user->get('usr_token_id'),
                    ],
                ],
                'errors' => [
                    'code'   => Response::STATUS_SUCCESS,
                    'detail' => '',
                ],
            ]
        );
    }

    public function loginUnknownUserValidToken(ApiTester $I)
    {
        $I->expectException(
            new Exception('User not found'),
            function () use ($I) {
                $this->addRecord($I);
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

                $record  = $I->getRecordWithFields(Users::class, ['usr_username' => 'testuser']);
                $dbToken = $record->get('usr_token_pre') . '.'
                         . $record->get('usr_token_mid') . '.'
                         . $record->get('usr_token_post');

                $response = $I->grabResponse();
                $response  = json_decode($response, true);
                $data      = $response['data'];
                $token     = $data['token'];
                $I->assertEquals($dbToken, $token);

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
            }
        );
    }

    private function addRecord(ApiTester $I)
    {
        return $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag'    => 1,
                'usr_username'       => 'testuser',
                'usr_password'       => 'testpassword',
                'usr_domain_name'    => 'https://niden.net',
                'usr_token_password' => '12345',
                'usr_token_pre'      => '',
                'usr_token_mid'      => '',
                'usr_token_post'     => '',
                'usr_token_id'       => '110011',
            ]
        );
    }
}

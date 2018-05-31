<?php

namespace Niden\Tests\api;

use ApiTester;
use Lcobucci\JWT\Builder;
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

//    public function loginKnownUserValidToken(ApiTester $I)
//    {
//        $this->addRecord($I);
//
//        $I->deleteHeader('Authorization');
//        $I->sendPOST(
//            '/login',
//            json_encode(
//                [
//                    'data' => [
//                        'username' => 'testuser',
//                        'password' => 'testpassword',
//                    ]
//                ]
//            )
//        );
//        $I->seeResponseIsSuccessful();
//
//        $record  = $I->getRecordWithFields(Users::class, ['usr_username' => 'testuser']);
//        $dbToken = $record->get('usr_token_pre') . '.'
//                 . $record->get('usr_token_mid') . '.'
//                 . $record->get('usr_token_post');
//
//        $response = $I->grabResponse();
//        $response  = json_decode($response, true);
//        $data      = $response['data'];
//        $token     = $data['token'];
//        $I->assertEquals($dbToken, $token);
//
//        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
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
//        $I->deleteHeader('Authorization');
//        $I->seeResponseIsSuccessful();
//        $I->seeResponseContainsJson(
//            [
//                'jsonapi' => [
//                    'version' => '1.0',
//                ],
//                'data'   => [
//                    'Hello',
//                ],
//                'errors' => [
//                    'code'   => Response::STATUS_SUCCESS,
//                    'detail' => '',
//                ],
//            ]
//        );
//    }

    private function addRecord(ApiTester $I)
    {
        $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag' => 1,
                'usr_username'    => 'testuser',
                'usr_password'    => 'testpassword',
                'usr_domain_name' => 'https://niden.net',
                'usr_token_pre'   => '',
                'usr_token_mid'   => '',
                'usr_token_post'  => '',
                'usr_token_id'    => '110011',
            ]
        );
    }
//
//    private function getToken()
//    {
//        $builder = new Builder();
//        $token   = $builder
//            ->setIssuer('https://phalconphp.com')
//            ->setAudience('https://niden.net')
//            ->setId('110011', true)
//            ->setIssuedAt(time())
//            ->setNotBefore(time() + 60)
//            ->setExpiration(time() + 3600)
//            ->getToken();
//
//        return $token->__toString();
//    }
}

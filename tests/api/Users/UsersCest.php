<?php

namespace Niden\Tests\api\Users;

use ApiTester;
use Niden\Models\Users;

class UsersCest
{
    public function getManyUsers(ApiTester $I)
    {
        $userOne = $I->haveRecordWithFields(
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

        $userTwo = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag'    => 1,
                'usr_username'       => 'testuser1',
                'usr_password'       => 'testpassword1',
                'usr_domain_name'    => 'https://niden.net',
                'usr_token_password' => '789789',
                'usr_token_pre'      => '',
                'usr_token_mid'      => '',
                'usr_token_post'     => '',
                'usr_token_id'       => '001100',
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
        $I->sendPOST('/users/get');
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'data'   => [
                    [
                        'id'            => $userOne->get('usr_id'),
                        'status'        => $userOne->get('usr_status_flag'),
                        'username'      => $userOne->get('usr_username'),
                        'domainName'    => $userOne->get('usr_domain_name'),
                        'tokenPassword' => $userOne->get('usr_token_password'),
                        'tokenId'       => $userOne->get('usr_token_id'),
                    ],
                    [
                        'id'            => $userTwo->get('usr_id'),
                        'status'        => $userTwo->get('usr_status_flag'),
                        'username'      => $userTwo->get('usr_username'),
                        'domainName'    => $userTwo->get('usr_domain_name'),
                        'tokenPassword' => $userTwo->get('usr_token_password'),
                        'tokenId'       => $userTwo->get('usr_token_id'),
                    ],
                ],
            ]
        );
    }
}

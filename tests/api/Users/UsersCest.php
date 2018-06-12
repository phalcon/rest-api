<?php

namespace Niden\Tests\api\Users;

use ApiTester;
use Niden\Models\Users;
use Page\Data;

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
                'usr_issuer'         => 'https://niden.net',
                'usr_token_password' => '12345',
                'usr_token_id'       => '110011',
            ]
        );

        $userTwo = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag'    => 1,
                'usr_username'       => 'testuser1',
                'usr_password'       => 'testpassword1',
                'usr_issuer'         => 'https://niden.net',
                'usr_token_password' => '789789',
                'usr_token_id'       => '001100',
            ]
        );

        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(Data::$usersGetUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'            => $userOne->get('usr_id'),
                    'status'        => $userOne->get('usr_status_flag'),
                    'username'      => $userOne->get('usr_username'),
                    'issuer'        => $userOne->get('usr_issuer'),
                    'tokenPassword' => $userOne->get('usr_token_password'),
                    'tokenId'       => $userOne->get('usr_token_id'),
                ],
                [
                    'id'            => $userTwo->get('usr_id'),
                    'status'        => $userTwo->get('usr_status_flag'),
                    'username'      => $userTwo->get('usr_username'),
                    'issuer'        => $userTwo->get('usr_issuer'),
                    'tokenPassword' => $userTwo->get('usr_token_password'),
                    'tokenId'       => $userTwo->get('usr_token_id'),
                ],
            ]
        );
    }

    public function getManyUsersWithNoData(ApiTester $I)
    {
        $userOne = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag'    => 1,
                'usr_username'       => 'testuser',
                'usr_password'       => 'testpassword',
                'usr_issuer'         => 'https://niden.net',
                'usr_token_password' => '12345',
                'usr_token_id'       => '110011',
            ]
        );

        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(Data::$usersGetUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }
}

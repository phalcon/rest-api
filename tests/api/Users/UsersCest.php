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
                'usr_domain_name'    => 'https://niden.net',
                'usr_token_password' => '12345',
                'usr_token_pre'      => '',
                'usr_token_mid'      => '',
                'usr_token_post'     => '',
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

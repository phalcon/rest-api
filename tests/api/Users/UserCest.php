<?php

namespace Niden\Tests\api\Users;

use ApiTester;
use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Models\Users;
use Page\Data;

class UserCest
{
    public function loginKnownUserNoToken(ApiTester $I)
    {
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$userGetUrl, Data::userGetJson(1));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserIncorrectSignatureInToken(ApiTester $I)
    {
        $this->addRecord($I);
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();

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
        $I->sendPOST(Data::$userGetUrl, Data::userGetJson(1));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserIncorrectToken(ApiTester $I)
    {
        $this->addRecord($I);
        $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer abcde');
        $I->sendPOST(Data::$userGetUrl, Data::userGetJson(1));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserCorrectToken(ApiTester $I)
    {
        $this->addRecord($I);
        $I->apiLogin();
    }

    public function loginKnownUserValidToken(ApiTester $I)
    {
        $user  = $this->addRecord($I);
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(Data::$userGetUrl, Data::userGetJson($user->get('usr_id')));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'            => $user->get('usr_id'),
                    'status'        => $user->get('usr_status_flag'),
                    'username'      => $user->get('usr_username'),
                    'domainName'    => $user->get('usr_domain_name'),
                    'tokenPassword' => $user->get('usr_token_password'),
                    'tokenId'       => $user->get('usr_token_id'),
                ],
            ]
        );
    }

    public function loginUnknownUserValidToken(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(Data::$userGetUrl, Data::userGetJson(1));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Record not found');
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

<?php

namespace Niden\Tests\api\Users;

use ApiTester;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
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

    public function loginKnownUserExpiredToken(ApiTester $I)
    {
        $this->addRecord($I);
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();

        $record  = $I->getRecordWithFields(Users::class, ['usr_username' => 'testuser']);

        $signer  = new Sha512();
        $builder = new Builder();

        $token   = $builder
            ->setIssuer($record->get('usr_domain_name'))
            ->setAudience('https://phalconphp.com')
            ->setId($record->get('usr_token_id'), true)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpiration(time() - 3000)
            ->sign($signer, $record->get('usr_token_password'))
            ->getToken();

        $expiredToken = $token->__toString();

        list($pre, $mid, $post) = explode('.', $expiredToken);
        $result = $record
            ->set('usr_token_pre', $pre)
            ->set('usr_token_mid', $mid)
            ->set('usr_token_post', $post)
            ->save();
        $I->assertNotEquals(false, $result);

        $I->haveHttpHeader('Authorization', 'Bearer ' . $expiredToken);
        $I->sendPOST(Data::$userGetUrl, Data::userGetJson($record->get('usr_id')));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserInvalidToken(ApiTester $I)
    {
        $this->addRecord($I);
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();

        $record  = $I->getRecordWithFields(Users::class, ['usr_username' => 'testuser']);

        $signer  = new Sha512();
        $builder = new Builder();

        $token   = $builder
            ->setIssuer($record->get('usr_domain_name'))
            ->setAudience('https://phalconphp.com')
            ->setId($record->get('usr_token_id'), true)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpiration(time() - 3000)
            ->sign($signer, $record->get('usr_token_password'))
            ->getToken();

        $invalidToken = $token->__toString();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $invalidToken);
        $I->sendPOST(Data::$userGetUrl, Data::userGetJson($record->get('usr_id')));
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

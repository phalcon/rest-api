<?php

namespace Phalcon\Api\Tests\api\Users;

use ApiTester;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Traits\TokenTrait;
use Page\Data;

class GetCest
{
    use TokenTrait;

    public function loginKnownUserNoToken(ApiTester $I)
    {
        $I->deleteHeader('Authorization');
        $I->sendGET(Data::$usersUrl . '/1');
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserGetUnknownUser(ApiTester $I)
    {
        $I->addApiUserRecord();
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();

        $response = $I->grabResponse();
        $response = json_decode($response, true);
        $data     = $response['data'];
        $token    = $data['token'];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$usersUrl . '/1');
        $I->seeResponseIs404();
    }

    public function loginKnownUserIncorrectSignature(ApiTester $I)
    {
        $record = $I->addApiUserRecord();
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();

        $signer  = new Sha512();
        $builder = new Builder();

        $token = $builder
            ->setIssuer('https://niden.net')
            ->setAudience($this->getTokenAudience())
            ->setId('110011', true)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpiration(time() - 3000)
            ->sign($signer, '123456')
            ->getToken()
        ;

        $wrongToken = $token->__toString();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $wrongToken);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserExpiredToken(ApiTester $I)
    {
        $record = $I->addApiUserRecord();
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();

        $signer  = new Sha512();
        $builder = new Builder();

        $token = $builder
            ->setIssuer('https://niden.net')
            ->setAudience($this->getTokenAudience())
            ->setId('110011', true)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpiration(time() - 3000)
            ->sign($signer, '12345')
            ->getToken()
        ;

        $expiredToken = $token->__toString();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $expiredToken);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserInvalidToken(ApiTester $I)
    {
        $record = $I->addApiUserRecord();
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();

        $signer  = new Sha512();
        $builder = new Builder();

        $token = $builder
            ->setIssuer('https://niden.net')
            ->setAudience($this->getTokenAudience())
            ->setId('110011', true)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpiration(time() - 3000)
            ->sign($signer, '12345')
            ->getToken()
        ;

        $invalidToken = $token->__toString();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $invalidToken);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserInvalidUserInToken(ApiTester $I)
    {
        $record = $I->addApiUserRecord();
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();

        $signer  = new Sha512();
        $builder = new Builder();

        $token = $builder
            ->setIssuer('https://niden.com')
            ->setAudience($this->getTokenAudience())
            ->setId('110011', true)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpiration(time() - 3000)
            ->sign($signer, '12345')
            ->getToken()
        ;

        $invalidToken = $token->__toString();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $invalidToken);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token');
    }

    public function loginKnownUserCorrectToken(ApiTester $I)
    {
        $I->addApiUserRecord();
        $I->apiLogin();
    }

    public function loginKnownUserValidToken(ApiTester $I)
    {
        $record = $I->addApiUserRecord();
        $token  = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::userResponse($record),
            ]
        );
    }

    public function getManyUsers(ApiTester $I)
    {
        $userOne = $I->haveRecordWithFields(
            Users::class,
            [
                'status'        => 1,
                'username'      => 'testuser',
                'password'      => 'testpassword',
                'issuer'        => 'https://niden.net',
                'tokenPassword' => '12345',
                'tokenId'       => '110011',
            ]
        );

        $userTwo = $I->haveRecordWithFields(
            Users::class,
            [
                'status'        => 1,
                'username'      => 'testuser1',
                'password'      => 'testpassword1',
                'issuer'        => 'https://niden.net',
                'tokenPassword' => '789789',
                'tokenId'       => '001100',
            ]
        );

        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$usersUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::userResponse($userOne),
                Data::userResponse($userTwo),
            ]
        );
    }

    public function getManyUsersWithNoData(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$usersUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
    }
}

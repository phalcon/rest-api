<?php

namespace Phalcon\Api\Tests\api\Users;

use ApiTester;
use Page\Data;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;

use function explode;
use function usleep;

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

        $signer  = new Hmac();
        $builder = new Builder($signer);

        $token = $builder
            ->setIssuer(Data::$testIssuer)
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpirationTime(time() + 3000)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $wrongToken = $token->getToken();
        $parts      = explode('.', $wrongToken);
        $wrongToken = $parts[0] . '.' . $parts[1] . '.';

        $I->haveHttpHeader('Authorization', 'Bearer ' . $wrongToken);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token (verification)');
    }

    public function loginKnownUserExpiredToken(ApiTester $I)
    {
        $record = $I->addApiUserRecord();
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();

        $signer  = new Hmac();
        $builder = new Builder($signer);

        $token = $builder
            ->setIssuer(Data::$testIssuer)
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpirationTime(time())
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        usleep(1000000);
        $expiredToken = $token->getToken();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $expiredToken);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token (verification)');
    }

    public function loginKnownUserInvalidToken(ApiTester $I)
    {
        $record = $I->addApiUserRecord();
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();

        $signer  = new Hmac();
        $builder = new Builder($signer);

        $token = $builder
            ->setIssuer(Data::$testIssuer)
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpirationTime(time() + 3000)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $invalidToken = $token->getToken() . 'xx';

        $I->haveHttpHeader('Authorization', 'Bearer ' . $invalidToken);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token (verification)');
    }

    public function loginKnownUserInvalidUserInToken(ApiTester $I)
    {
        $record = $I->addApiUserRecord();
        $I->deleteHeader('Authorization');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();

        $signer  = new Hmac();
        $builder = new Builder($signer);

        $token = $builder
            ->setIssuer(Data::$testIssuer)
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpirationTime(time() + 3000)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $invalidToken = $token->getToken();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $invalidToken);
        $I->sendGET(Data::$usersUrl . '/' . $record->get('id'));
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Invalid Token (verification)');
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
                'username'      => Data::$testUsername,
                'password'      => Data::$testPassword,
                'issuer'        => Data::$testIssuer,
                'tokenPassword' => Data::$strongPassphrase,
                'tokenId'       => Data::$testTokenId,
            ]
        );

        $userTwo = $I->haveRecordWithFields(
            Users::class,
            [
                'status'        => 1,
                'username'      => Data::$testUsername . '1',
                'password'      => Data::$testPassword . '1',
                'issuer'        => Data::$testIssuer . '1',
                'tokenPassword' => Data::$strongPassphrase . '1',
                'tokenId'       => Data::$testTokenId . '1',
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

<?php

namespace Phalcon\Api\Tests\api;

use ApiTester;
use Page\Data;
use Phalcon\Api\Constants\Flags;
use Phalcon\Api\Models\Users;

use function json_decode;

class LoginCest
{
    public function loginUnknownUser(ApiTester $I)
    {
        $I->sendPOST(
            Data::$loginUrl,
            [
                'username' => 'user',
                'password' => 'pass',
            ]
        );
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Incorrect credentials');
    }

    public function loginKnownUser(ApiTester $I)
    {
        $I->haveRecordWithFields(
            Users::class,
            [
                'status'        => Flags::ACTIVE,
                'username'      => Data::$testUsername,
                'password'      => Data::$testPassword,
                'issuer'        => Data::$testIssuer,
                'tokenPassword' => Data::$strongPassphrase,
                'tokenId'       => Data::$testTokenId,
            ]
        );

        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data     = json_decode($response, true);
        $I->assertTrue(isset($data['data']));
        $I->assertTrue(isset($data['data']['token']));
        $I->assertTrue(isset($data['meta']));
    }
}

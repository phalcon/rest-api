<?php

namespace Gewaer\Tests\api;

use ApiTester;

class LanguagesCest
{
    public function list(ApiTester $I)
    {
        $userData = $I->apiLogin();
        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet('/v1/languages');
        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);
        $I->assertTrue(isset($data[0]['id']));
    }
}

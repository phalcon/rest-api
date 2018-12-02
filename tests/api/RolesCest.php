<?php

namespace Gewaer\Tests\api;

use ApiTester;

class RolesCest
{
    public function list(ApiTester $I)
    {
        $userData = $I->apiLogin();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet('/v1/roles');

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue(isset($data[0]['id']));
    }
}

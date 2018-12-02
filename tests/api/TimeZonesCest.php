<?php

namespace Gewaer\Tests\api;

use ApiTester;

class TimeZonesCest
{
    /**
     * List of timezones
     *
     * @param ApiTester $I
     * @return void
     */
    public function list(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet('/v1/timezones');
        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);
        $I->assertTrue(isset($data[0]));
    }
}

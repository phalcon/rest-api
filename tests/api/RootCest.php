<?php

namespace Niden\Tests\api;

use ApiTester;
use function floatval;
use Page\Data;

class RootCest
{
    public function checkDefaultRoute(ApiTester $I)
    {
        $I->sendGET(Data::$rootUrl);
        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $response = json_decode($response, true);
        $I->assertEquals("3.1416", $response['data'][0]);
    }
}

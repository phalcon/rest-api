<?php

namespace Niden\Tests\api;

use ApiTester;
use function floatval;

class RootCest
{
    public function checkDefaultRoute(ApiTester $I)
    {
        $I->sendGET('/');
//        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $response = json_decode($response, true);
        $I->assertEquals(3.1416, $response['data'][0]);
    }
}

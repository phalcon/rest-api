<?php

namespace Niden\Tests\api;

use ApiTester;
use function floatval;

class RootCest
{
    public function checkDefaultRoute(ApiTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $response = json_decode($response, true);
        $pi = floatval(substr($response['data'][0], 0, 4));
        $I->assertEquals(3.14, $pi);
    }
}

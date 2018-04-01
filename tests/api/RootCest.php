<?php

namespace Niden\Tests\api;
use ApiTester;

class RootCest
{
    public function checkDefaultRoute(ApiTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('Phalcon API');
    }
}

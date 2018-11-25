<?php

namespace Gewaer\Tests\api;

use ApiTester;
use Page\Data;

class StatusCest
{
    public function checkNotFoundRoute(ApiTester $I)
    {
        $I->sendGET(Data::$statusUrl);
        $I->seeResponseCodeIs('200');
    }
}

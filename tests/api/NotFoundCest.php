<?php

namespace Niden\Tests\api;

use ApiTester;
use Page\Data;

class NotFoundCest
{
    public function checkNotFoundRoute(ApiTester $I)
    {
        $I->sendGET(Data::$wrongUrl);
        $I->seeResponseIs404();
    }
}

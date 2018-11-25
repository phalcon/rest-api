<?php

namespace Gewaer\Tests\api;

use ApiTester;
use Page\Data;

class NotFoundCest
{
    public function checkNotFoundRoute(ApiTester $I)
    {
        $I->sendGET(Data::$wrongUrl);

        $I->seeResponseCodeIs('404');
    }
}

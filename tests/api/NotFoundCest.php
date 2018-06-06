<?php

namespace Niden\Tests\api;

use ApiTester;
use Niden\Exception\Exception;
use Niden\Http\Response;
use Page\Data;

class NotFoundCest
{
    public function checkNotFoundRoute(ApiTester $I)
    {
        $I->sendGET(Data::$wrongUrl);
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('404 Not Found');
    }
}

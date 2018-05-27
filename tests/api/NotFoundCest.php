<?php

namespace Niden\Tests\api;

use ApiTester;
use Niden\Exception\Exception;
use Niden\Http\Response;

class NotFoundCest
{
    public function checkNotFoundRoute(ApiTester $I)
    {
        $I->expectException(
            new Exception('404 Not Found'),
            function () use ($I) {
                $I->sendGET('/something');
            }
        );
    }
}

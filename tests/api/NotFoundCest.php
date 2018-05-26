<?php

namespace Niden\Tests\api;

use ApiTester;
use Niden\Http\Response;

class NotFoundCest
{
    public function checkNotFoundRoute(ApiTester $I)
    {
        $I->sendGET('/something');
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'data'   => [],
                'errors' => [
                    'code'   => Response::STATUS_ERROR,
                    'detail' => '404 Not Found',
                ],
            ]
        );
    }
}

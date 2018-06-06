<?php

namespace Niden\Tests\api;

use ApiTester;
use function floatval;
use Niden\Exception\Exception;
use Niden\Http\Response;
use Page\Data;

class IncorrectPayloadCest
{
    public function checkDefaultRoute(ApiTester $I)
    {
        $I->sendPOST(Data::$userGetUrl, '{"key": "value}');
        $I->seeResponseIsSuccessful();
        $I->seeErrorJsonResponse('Malformed JSON');
    }
}

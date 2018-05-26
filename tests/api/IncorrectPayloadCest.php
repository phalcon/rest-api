<?php

namespace Niden\Tests\api;

use ApiTester;
use function floatval;
use Niden\Http\Response;

class IncorrectPayloadCest
{
    public function checkDefaultRoute(ApiTester $I)
    {
        $I->sendPOST(
            '/',
            '{"key": "value}'
        );
        $I->seeResponseIsSuccessful();
        $I->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'data'   => [],
                'errors' => [
                    'code'   => Response::STATUS_ERROR,
                    'detail' => 'Malformed JSON',
                ],
            ]
        );
    }
}

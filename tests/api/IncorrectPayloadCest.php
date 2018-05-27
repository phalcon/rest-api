<?php

namespace Niden\Tests\api;

use ApiTester;
use function floatval;
use Niden\Exception\Exception;
use Niden\Http\Response;

class IncorrectPayloadCest
{
    public function checkDefaultRoute(ApiTester $I)
    {
        $I->expectException(
            new Exception('Malformed JSON'),
            function () use ($I) {
                $I->sendPOST(
                    '/',
                    '{"key": "value}'
                );
            }
        );
    }
}

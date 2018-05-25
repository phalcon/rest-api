<?php

namespace Niden\Tests\unit\library\Http;

use Niden\Http\Request;
use \UnitTester;

class RequestCest
{
    public function checkBearerHeader(UnitTester $I)
    {
        $request = new Request();

        $I->assertEmpty($request->getBearerTokenFromHeader());
    }
}

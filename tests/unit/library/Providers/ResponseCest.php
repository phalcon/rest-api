<?php

namespace Niden\Tests\unit\library\Providers;

use Niden\Http\Response;
use Niden\Providers\ResponseProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;

class ResponseCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new ResponseProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('response'));
        /** @var Response $response */
        $response = $diContainer->getShared('response');
        $I->assertTrue($response instanceof Response);
    }
}

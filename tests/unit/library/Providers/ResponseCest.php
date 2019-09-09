<?php

namespace Phalcon\Api\Tests\unit\library\Providers;

use Phalcon\Api\Http\Response;
use Phalcon\Api\Providers\ResponseProvider;
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

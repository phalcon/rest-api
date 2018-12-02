<?php

namespace Gewaer\Tests\unit\library\Providers;

use Gewaer\Providers\RequestProvider;
use Gewaer\Providers\ConfigProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;
use Gewaer\Http\Request;

class RequestCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider = new ConfigProvider();
        $provider->register($diContainer);

        $provider = new RequestProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('request'));

        $request = $diContainer->getShared('request');
        $I->assertTrue($request instanceof Request);
    }
}

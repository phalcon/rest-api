<?php

namespace Gewaer\Tests\unit\library\Providers;

use Gewaer\Providers\SessionProvider;
use Gewaer\Providers\ConfigProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;
use Phalcon\Session\Adapter\Libmemcached;

class SessionCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider = new ConfigProvider();
        $provider->register($diContainer);

        $provider = new SessionProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('session'));

        $session = $diContainer->getShared('session');
        $I->assertTrue($session instanceof Libmemcached);
    }
}

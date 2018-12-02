<?php

namespace Gewaer\Tests\unit\library\Providers;

use Gewaer\Providers\AclProvider;
use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\DatabaseProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;
use Gewaer\Acl\Manager as AclManager;

class AclCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new DatabaseProvider();
        $provider->register($diContainer);
        $provider = new AclProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('acl'));

        $acl = $diContainer->getShared('acl');
        $I->assertTrue($acl instanceof AclManager);
    }
}

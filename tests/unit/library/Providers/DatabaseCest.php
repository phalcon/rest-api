<?php

namespace Niden\Tests\unit\library\Providers;

use Niden\Providers\ConfigProvider;
use Niden\Providers\DatabaseProvider;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\FactoryDefault;
use UnitTester;

class DatabaseCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new DatabaseProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('db'));
        /** @var Mysql $db */
        $db = $diContainer->getShared('db');
        $I->assertTrue($db instanceof Mysql);
        $I->assertEquals('mysql', $db->getType());
    }
}

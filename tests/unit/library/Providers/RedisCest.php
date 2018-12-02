<?php

namespace Gewaer\Tests\unit\library\Providers;

use Gewaer\Providers\RedisProvider;
use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\DatabaseProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;
use Redis;

class RedisCest
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
        $provider = new RedisProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('redis'));

        $redis = $diContainer->getShared('redis');
        $I->assertTrue($redis instanceof Redis);
    }
}

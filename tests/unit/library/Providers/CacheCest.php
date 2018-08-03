<?php

namespace Niden\Tests\unit\library\Providers;

use Niden\Providers\CacheDataProvider;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Di\FactoryDefault;
use UnitTester;

class CacheCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new CacheDataProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('cache'));
        /** @var Libmemcached $cache */
        $cache = $diContainer->getShared('cache');
        $I->assertTrue($cache instanceof Libmemcached);
    }
}

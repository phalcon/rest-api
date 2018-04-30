<?php

namespace Niden\Tests\unit\library\Providers;

use Niden\Providers\ConfigProvider;
use Phalcon\Di\FactoryDefault;
use \UnitTester;

class ConfigCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);

//        $I->assertTrue($diContainer->has('config'));
        $config = $diContainer->getShared('config')->toArray();

        $I->assertTrue(isset($config['app']['version']));
        $I->assertTrue(isset($config['app']['timezone']));
        $I->assertTrue(isset($config['app']['debug']));
        $I->assertTrue(isset($config['app']['env']));
        $I->assertTrue(isset($config['app']['devMode']));
        $I->assertTrue(isset($config['app']['baseUri']));
        $I->assertTrue(isset($config['app']['supportEmail']));
        $I->assertTrue(isset($config['app']['time']));
    }
}

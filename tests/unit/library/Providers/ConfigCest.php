<?php

namespace Niden\Tests\unit\library\Providers;

use Niden\Providers\ConfigProvider;
use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use UnitTester;

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

        $I->assertTrue($diContainer->has('config'));
        $config = $diContainer->getShared('config');
        $I->assertTrue($config instanceof Config);

        $configArray = $config->toArray();
        $I->assertTrue(isset($configArray['app']['version']));
        $I->assertTrue(isset($configArray['app']['timezone']));
        $I->assertTrue(isset($configArray['app']['debug']));
        $I->assertTrue(isset($configArray['app']['env']));
        $I->assertTrue(isset($configArray['app']['devMode']));
        $I->assertTrue(isset($configArray['app']['baseUri']));
        $I->assertTrue(isset($configArray['app']['supportEmail']));
        $I->assertTrue(isset($configArray['app']['time']));
        $I->assertTrue(isset($configArray['cache']));
    }
}

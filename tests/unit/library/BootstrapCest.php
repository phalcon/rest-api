<?php

namespace Gewaer\Tests\unit\library;

use Gewaer\Bootstrap\Api;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use UnitTester;

class BootstrapCest
{
    public function checkBootstrap(UnitTester $I)
    {
        $bootstrap = new Api();
        $bootstrap->setup();

        $I->assertTrue($bootstrap->getContainer() instanceof FactoryDefault);
        $I->assertTrue($bootstrap->getApplication() instanceof Micro);
    }
}

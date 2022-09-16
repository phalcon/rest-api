<?php

namespace Phalcon\Api\Tests\unit\library;

use Phalcon\Api\Bootstrap\Api;
use Phalcon\Api\Http\Response;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use UnitTester;

class BootstrapCest
{
    public function checkBootstrap(UnitTester $I)
    {
        $bootstrap = new Api();

        $I->assertTrue($bootstrap->getContainer() instanceof FactoryDefault);
        $I->assertTrue($bootstrap->getResponse() instanceof Response);
        $I->assertTrue($bootstrap->getApplication() instanceof Micro);
    }
}

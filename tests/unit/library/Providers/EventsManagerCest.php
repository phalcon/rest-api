<?php

namespace Niden\Tests\unit\library\Providers;

use Niden\Providers\EventsManagerProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager;
use \UnitTester;

class EventsManagerCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new EventsManagerProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('eventsManager'));
        $eventsManager = $diContainer->getShared('eventsManager');
        $I->assertTrue($eventsManager instanceof Manager);
    }
}

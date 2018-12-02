<?php

namespace Gewaer\Tests\unit\library\Providers;

use Gewaer\Providers\QueueProvider;
use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\DatabaseProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;
use Phalcon\Queue\Beanstalk\Extended as Beanstalk;

class QueueCest
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
        $provider = new QueueProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('queue'));

        $queue = $diContainer->getShared('queue');
        $I->assertTrue($queue instanceof Beanstalk);
    }
}

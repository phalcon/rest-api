<?php

namespace Gewaer\Tests\unit\library\Providers;

use Gewaer\Providers\MailProvider;
use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\DatabaseProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;
use Baka\Mail\Message;

class MailCest
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
        $provider = new MailProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('mail'));

        $mail = $diContainer->getShared('mail');
        $I->assertTrue($mail instanceof Message);
    }
}

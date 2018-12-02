<?php

namespace Gewaer\Tests\integration\library\Models;

use Gewaer\Models\Apps;
use IntegrationTester;
use Gewaer\Providers\ConfigProvider;
use Phalcon\Di\FactoryDefault;

class AppsCest
{
    /**
     * Confirm the default apps exist
     *
     * @param IntegrationTester $I
     * @return void
     */
    public function getDefaultApp(IntegrationTester $I)
    {
        $app = Apps::getACLApp(Apps::GEWAER_DEFAULT_APP_NAME);
        $I->assertTrue($app->name == Apps::GEWAER_DEFAULT_APP_NAME);
    }

    /**
     * Confirm the default apps exist
     *
     * @param IntegrationTester $I
     * @return void
     */
    public function getCanvasApp(IntegrationTester $I)
    {
        $diContainer = new FactoryDefault();

        $provider = new ConfigProvider();
        $provider->register($diContainer);

        $app = Apps::getACLApp('Canvas');
        $I->assertTrue($app->getId() == $diContainer->getShared('config')->app->id);
    }
}

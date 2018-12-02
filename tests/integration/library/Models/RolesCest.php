<?php

namespace Gewaer\Tests\integration\library\Models;

use Gewaer\Models\Apps;
use IntegrationTester;
use Gewaer\Providers\ConfigProvider;
use Phalcon\Di\FactoryDefault;
use Gewaer\Models\Roles;
use Gewaer\Models\Companies;

class RolesCest
{
    /**
     * Confirm the default apps exist
     *
     * @param IntegrationTester $I
     * @return void
     */
    public function getByAppName(IntegrationTester $I)
    {
        $diContainer = new FactoryDefault();

        $provider = new ConfigProvider();
        $provider->register($diContainer);

        $company = Companies::findFirst(1);
        $role = Roles::getByAppName('Default.Admins', $company);

        $I->assertTrue($role->name == 'Admins');
    }
}

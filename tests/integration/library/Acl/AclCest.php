<?php

namespace Gewaer\Tests\integration\library\Acl;

use IntegrationTester;
use Gewaer\Acl\Manager as AclManager;

class AclCest
{
    public function validateCreateRole(IntegrationTester $I)
    {
        $acl = new AclManager(
            [
                'db' => $I->grabFromDi('db'),
                'roles' => 'roles',
                'rolesInherits' => 'roles_inherits',
                'resources' => 'resources',
                'resourcesAccesses' => 'resources_accesses',
                'accessList' => 'access_list'
            ]
        );
    }
}

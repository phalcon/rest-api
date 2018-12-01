<?php

namespace Gewaer\Tests\integration\library\Acl;

use IntegrationTester;
use Gewaer\Acl\Manager as AclManager;
use Phalcon\Di\FactoryDefault;
use Gewaer\Providers\AclProvider;
use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\DatabaseProvider;
use Gewaer\Models\Users;

class AclCest
{
    /**
     * Initiliaze ACL
     *
     * @return void
     */
    protected function aclService(): AclManager
    {
        $diContainer = new FactoryDefault();
        $provider = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new DatabaseProvider();
        $provider->register($diContainer);
        $provider = new AclProvider();
        $provider->register($diContainer);

        return $diContainer->getShared('acl');
    }

    public function validateAclService(IntegrationTester $I)
    {
        $acl = $this->aclService();
        $I->assertTrue($acl instanceof AclManager);
    }

    public function checkCreateRole(IntegrationTester $I)
    {
        $acl = $this->aclService();

        $I->assertTrue($acl->addRole(new \Phalcon\Acl\Role('Admins')));
    }

    public function checkAddResource(IntegrationTester $I)
    {
        $acl = $this->aclService();

        $I->assertTrue($acl->addResource('Default.Users', ['list', 'create', 'edit', 'delete']));
    }

    public function checkAllowPermission(IntegrationTester $I)
    {
        $acl = $this->aclService();

        $I->assertTrue($acl->allow('Admins', 'Default.Users', ['list', 'create']));
    }

    public function checkDenyPermission(IntegrationTester $I)
    {
        $acl = $this->aclService();

        $I->assertTrue($acl->deny('Admins', 'Default.Users', ['edit', 'delete']));
    }

    public function checkIsAllowPermission(IntegrationTester $I)
    {
        $acl = $this->aclService();

        $I->assertTrue($acl->isAllowed('Admins', 'Default.Users', 'list'));
    }

    public function checkIsDeniedPermission(IntegrationTester $I)
    {
        $acl = $this->aclService();

        $I->assertTrue(!$acl->isAllowed('Admins', 'Default.Users', 'edit'));
    }

    public function checkSetAppByRole(IntegrationTester $I)
    {
        $acl = $this->aclService();

        $I->assertTrue($acl->addRole('Default.Admins'));
    }

    public function checkUsersAssignRole(IntegrationTester $I)
    {
        $acl = $this->aclService();
        $userData = Users::findFirst(1);

        $I->assertTrue($userData->assignRole('Default.Admins'));
    }

    public function checkUsersHasPermission(IntegrationTester $I)
    {
        $acl = $this->aclService();
        $userData = Users::findFirst(1);

        $I->assertTrue($userData->can('Users.create'));
    }

    public function checkUsersDoesntHavePermission(IntegrationTester $I)
    {
        $acl = $this->aclService();
        $userData = Users::findFirst(1);

        $I->assertFalse($userData->can('Users.delete'));
    }

    public function checkUsersRemoveRole(IntegrationTester $I)
    {
        $acl = $this->aclService();
        $userData = Users::findFirst(1);

        $I->assertTrue($userData->removeRole('Default.Admins'));
    }
}

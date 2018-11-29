<?php

declare(strict_types=1);

namespace Gewaer\Traits;

use Gewaer\Models\Roles;
use Gewaer\Models\UserRoles;
use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Exception\ModelException;

/**
 * Trait FractalTrait
 *
 * @package Gewaer\Traits
 */
trait PermissionsTrait
{
    /**
     * Assigne a user this role
     * Example: App.Role
     *
     * @param string $role
     * @return boolean
     */
    public function assignRole(string $role): bool
    {
        $role = Roles::getByAppName($role, $this->defaultCompany);

        if (!$role) {
            throw new ServerErrorHttpException('Role not found in DB');
        }

        $userRole = UserRoles::findFirst([
            'conditions' => 'users_id = ?0 and roles_id = ?1 and apps_id = ?2 and company_id = ?3',
            'bind' => [$this->getId(), $role->getId(), $role->apps_id, $this->default_company]
        ]);

        if (!$userRole) {
            $userRole = new UserRoles();
            $userRole->users_id = $this->getid();
            $userRole->roles_id = $role->getId();
            $userRole->apps_id = $role->apps_id;
            $userRole->company_id = $this->default_company;
            if (!$userRole->save()) {
                throw new ModelException((string) current($userRole->getMessages()));
            }
        }

        return true;
    }

    /**
     * Remove a role for the current user
     * Example: App.Role
     *
     * @param string $role
     * @return boolean
     */
    public function removeRole(string $role): bool
    {
        $role = Roles::getByAppName($role, $this->defaultCompany);

        if (!$role) {
            throw new ServerErrorHttpException('Role not found in DB');
        }

        $userRole = UserRoles::findFirst([
            'conditions' => 'users_id = ?0 and roles_id = ?1 and apps_id = ?2 and company_id = ?3',
            'bind' => [$this->getId(), $role->getId(), $role->apps_id, $this->default_company]
        ]);

        if ($userRole) {
            return $userRole->delete();
        }

        return false;
    }

    /**
     * Check if the user has this role
     *
     * @param string $role
     * @return boolean
     */
    public function hasRole(string $role): bool
    {
        $role = Roles::getByAppName($role, $this->defaultCompany);

        if (!$role) {
            throw new ServerErrorHttpException('Role not found in DB');
        }

        $userRole = UserRoles::findFirst([
            'conditions' => 'users_id = ?0 and roles_id = ?1 and apps_id = ?2 and company_id = ?3',
            'bind' => [$this->getId(), $role->getId(), $role->apps_id, $this->default_company]
        ]);

        if ($userRole) {
            return true;
        }

        return false;
    }

    /**
     * At this current system / app can you do this?
     *
     * Example: resource.action
     *  Leads.add || leads.updates || lead.delete
     *
     * @param string $action
     * @return boolean
     */
    public function can(string $action): bool
    {
        //get current role for this company App.Role
        // Section.Action
        //action is going to be resource.action so we need to explode it

        $userRole = UserRoles::findFirst([
            'conditions' => 'users_id = ?0 and apps_id in ( ?1, ?2) and company_id = ?3',
            'bind' => [$this->getId(), $this->di->getConfig()->app->id, Roles::DEFAULT_ACL_APP_ID, $this->default_company]
        ]);

        if (!$userRole) {
            throw new ServerErrorHttpException('ACL - You dont have acces to this role for this app ');
        }

        //if we find the . then les
        if (strpos($action, '.') == false) {
            throw new ServerErrorHttpException('ACL - We are expecting the resource for this action');
        }

        $action = explode('.', $action);
        $resource = $action[0];
        $action = $action[1];
        $app = $userRole->app->name;

        return $this->di->getAcl()->isAllowed($userRole->roles->name, ucfirst($app) . '.' . $resource, $action);
    }
}

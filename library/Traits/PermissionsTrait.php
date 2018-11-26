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
     *
     * @param string $role
     * @return boolean
     */
    public function assignRole(string $role): bool
    {
        $role = Roles::findFirst([
            'conditions' => 'name = ? and company_id = ? and apps_id = ?',
            'bind' => [$role, $this->userData->default_company, 0]
        ]);

        if (!$role) {
            throw new ServerErrorHttpException('Role not found in DB');
        }

        $userRole = UserRoles::findFirst([
            'conditions' => 'user_id = ? and roles_id = ? and apps_id =? and company_id = ?',
            'bind' => [$this->userData->getId(), $role->getId(), 0, $this->userData->default_company]
        ]);

        if (!$userRole) {
            $userRole = new UserRoles();
            $userRole->user_id = $this->userData->getid();
            $userRole->roles_id = $this->userData->getid();
            $userRole->apps_id = $this->userData->getid();
            $userRole->company_id = $this->userData->getid();
            if (!$userRole->save()) {
                throw new ModelException($userRole->getMessages());
            }

            return true;
        }

        return false;
    }

    /**
     * Remove a role for the current user
     *
     * @param string $role
     * @return boolean
     */
    public function removeRole(string $role): bool
    {
        $role = Roles::findFirst([
            'conditions' => 'name = ? and company_id = ? and apps_id = ?',
            'bind' => [$role, $this->userData->default_company, 0]
        ]);

        if (!$role) {
            throw new ServerErrorHttpException('Role not found in DB');
        }

        $userRole = UserRoles::findFirst([
            'conditions' => 'user_id = ? and roles_id = ? and apps_id =? and company_id = ?',
            'bind' => [$this->userData->getId(), $role->getId(), 0, $this->userData->default_company]
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
        $role = Roles::findFirst([
            'conditions' => 'name = ? and company_id = ? and apps_id = ?',
            'bind' => [$role, $this->userData->default_company, 0]
        ]);

        if (!$role) {
            throw new ServerErrorHttpException('Role not found in DB');
        }

        $userRole = UserRoles::findFirst([
            'conditions' => 'user_id = ? and roles_id = ? and apps_id =? and company_id = ?',
            'bind' => [$this->userData->getId(), $role->getId(), 0, $this->userData->default_company]
        ]);

        if ($userRole) {
            return true;
        }

        return false;
    }

    /**
     * At this current system / app can you do this?
     *
     * @param string $action
     * @return boolean
     */
    public function can(string $action): bool
    {
        //get current role for this company
        //action is going to be resource.action so we need to explode it

        return $this->acl->isAllowed('Admins', 'Products', 'update');
    }
}

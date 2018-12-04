<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Traits\PermissionsTrait;
use Gewaer\Exception\ModelException;
use Phalcon\Cashier\Billable;

class Users extends \Baka\Auth\Models\Users
{
    use PermissionsTrait;
    use Billable;

    public $roles_id;
    public $stripe_id;
    public $card_last_four;
    public $card_brand;
    public $trial_ends_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('users');

        $this->hasOne(
            'id',
            'Gewaer\Models\UserRoles',
            'users_id',
            ['alias' => 'permission']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\UserRoles',
            'users_id',
            ['alias' => 'permissions']
        );

        $this->hasManyToMany(
            'id',
            'Gewaer\Models\UserRoles',
            'users_id',
            'roles_id',
            'Gewaer\Models\Roles',
            'id',
            [
                'alias' => 'roles',
                'params' => [
                    'limit' => 1,
                    'conditions' => 'Gewaer\Models\UserRoles.apps_id = ' . $this->di->getConfig()->app->id,
                ]
            ]
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'users';
    }

    /**
     * Get the User key for redis
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->id;
    }

    /**
     * What to do after the creation of a new users
     *  - Assign default role
     *
     * @return void
     */
    public function afterCreate()
    {
        parent::afterCreate();

        //Assign admin role to the system if we dont get a specify role
        if (empty($this->roles_id)) {
            $role = Roles::findFirstByName('Admins');
            $this->roles_id = $role->getId();
            $this->update();

            $userRoles = new UserRoles();
            $userRoles->users_id = $this->getId();
            $userRoles->roles_id = $role->getId();
            $userRoles->apps_id = $this->di->getConfig()->app->id;
            $userRoles->company_id = $this->default_company;
            if (!$userRoles->save()) {
                throw new ModelException((string) current($userRoles->getMessages()));
            }
        }
    }
}

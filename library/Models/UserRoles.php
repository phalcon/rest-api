<?php
declare(strict_types=1);

namespace Gewaer\Models;

class UserRoles extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $users_id;

    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var integer
     */
    public $roles_id;

    /**
     *
     * @var integer
     */
    public $company_id;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var integer
     */
    public $is_deleted;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('user_roles');

        $this->belongsTo(
            'users_id',
            'Gewaer\Models\Users',
            'id',
            ['alias' => 'user']
        );

        $this->belongsTo(
            'roles_id',
            'Gewaer\Models\Roles',
            'id',
            ['alias' => 'roles']
        );

        $this->belongsTo(
            'apps_id',
            'Gewaer\Models\Apps',
            'id',
            ['alias' => 'app']
        );

        $this->belongsTo(
            'company_id',
            'Gewaer\Models\Companies',
            'id',
            ['alias' => 'company']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'user_roles';
    }
}

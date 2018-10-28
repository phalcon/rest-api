<?php
declare(strict_types=1);

namespace Gewaer\Models;

class AppsRoles extends \Baka\Auth\Models\AppsRoles
{
    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var string
     */
    public $roles_name;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('apps_roles');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'apps_roles';
    }
}

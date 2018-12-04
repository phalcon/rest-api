<?php

declare(strict_types=1);

namespace Gewaer\Models;

class AccessList extends AbstractModel
{
    /**
     *
     * @var string
     */
    public $roles_name;

    /**
     *
     * @var string
     */
    public $resource_name;

    /**
     *
     * @var string
     */
    public $access_name;

    /**
     *
     * @var integer
     */
    public $allowed;

    /**
     *
     * @var integer
     */
    public $apps_id;

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
        $this->setSource('access_list');

        $this->belongsTo(
            'roles_name',
            'Gewaer\Models\Roles',
            'name',
            ['alias' => 'role']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'access_list';
    }
}

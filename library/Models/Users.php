<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Traits\PermissionsTrait;

class Users extends \Baka\Auth\Models\Users
{
    use PermissionsTrait;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('users');
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
}

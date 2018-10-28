<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Baka\Models\Users;

/**
 * Base controller
 *
 */
class UsersController extends \Baka\Auth\UsersController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['name', 'firstname', 'lastname', 'displayname', 'email', 'password', 'created_at', 'updated_at', 'default_company', 'family'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['name', 'firstname', 'lastname', 'displayname', 'email', 'password', 'created_at', 'updated_at', 'default_company'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Users();
    }
}

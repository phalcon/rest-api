<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Roles;

/**
 * Class RolesController
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 */
class RolesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = [];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = [];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Roles();

        //get the list of roes for the systema + my company
        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['company_id', ':', '(0,' . $this->userData->default_company . ')'],
        ];
    }
}

<?php
declare(strict_types=1);

namespace Gewaer\Models;

class UsersAssociatedCompany extends \Baka\Auth\Models\UsersAssociatedCompany
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
    public $company_id;

    /**
     *
     * @var string
     */
    public $identify_id;

    /**
     *
     * @var integer
     */
    public $user_active;

    /**
     *
     * @var string
     */
    public $user_role;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('users_associated_company');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'users_associated_company';
    }
}

<?php
declare(strict_types=1);

namespace Gewaer\Models;

class UsersInvite extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $invite_hash;

    /**
     *
     * @var integer
     */
    public $company_id;

    /**
     *
     * @var integer
     */
    public $role_id;

    /**
     *
     * @var integer
     */
    public $app_id;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var integer
     */
    public $is_deleted;

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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('users_invite');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'users_invite';
    }
}

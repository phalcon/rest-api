<?php
declare(strict_types=1);

namespace Gewaer\Models;

class UserCompanyApps extends \Baka\Auth\Models\UserCompanyApps
{
    /**
     *
     * @var integer
     */
    public $company_id;

    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var string
     */
    public $stripe_id;

    /**
     *
     * @var integer
     */
    public $subscriptions_id;

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

        $this->setSource('user_company_apps');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'user_company_apps';
    }
}

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

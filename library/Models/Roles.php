<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Exception\ServerErrorHttpException;
use Baka\Auth\Models\Companies;

class Roles extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $description;

    /**
     *
     * @var integer
     */
    public $scope;

    /**
     *
     * @var integer
     */
    public $company_id;

    /**
     *
     * @var int
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
     * Default ACL company
     *
     */
    const DEFAULT_ACL_COMPANY_ID = 0;
    const DEFAULT_ACL_APP_ID = 0;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('roles');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'roles';
    }

    /**
     * Get the Role by it app name
     *
     * @param string $role
     * @return Roles
     */
    public function getByAppName(string $role, Companies $company): Roles
    {
        //echeck if we have a dot , taht means we are sending the specific app to use
        if (strpos($role, '.') == false) {
            throw new ServerErrorHttpException('ACL - We are expecting the app for this role');
        }

        $appRole = explode('.', $role);
        $role = $appRole[1];
        $appName = $appRole[0];

        //look for the app and set it
        if (!$app = Apps::getACLApp($appName)) {
            throw new ServerErrorHttpException('ACL - No app found for this role');
        }

        return self::findFirst([
            'conditions' => 'apps_id in (?0, ?1) AND company_id in (?2 , ?3)',
            'bind' => [$app->getId(), self::DEFAULT_ACL_APP_ID, $company->getId(), self::DEFAULT_ACL_COMPANY_ID]
        ]);
    }
}

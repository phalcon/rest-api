<?php
declare(strict_types=1);

namespace Gewaer\Acl;

use Phalcon\Db;
use Phalcon\Db\AdapterInterface as DbAdapter;
use Phalcon\Acl\Exception;
use Phalcon\Acl\Resource;
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\RoleInterface;
use Gewaer\Models\Companies;
use Gewaer\Models\Apps;
use Phalcon\Acl\Adapter\Database as PhalconAclDatabaseAdapter;
use Phalcon\Acl\Adapter;
use BadMethodCallException;

/**
 * Phalcon\Acl\Adapter\Database
 * Manages Geweaer Multi tenant ACL lists in database
 * 
 * #extends PhalconAclDatabaseAdapter #had to comments it out testing breaking
 */
class Manager extends Adapter
{
    /**
     * @var DbAdapter
     */
    protected $connection;

    /**
     * Roles table
     * @var string
     */
    protected $roles;

    /**
     * Resources table
     * @var string
     */
    protected $resources;

    /**
     * Resources Accesses table
     * @var string
     */
    protected $resourcesAccesses;

    /**
     * Access List table
     * @var string
     */
    protected $accessList;

    /**
     * Roles Inherits table
     * @var string
     */
    protected $rolesInherits;

    /**
     * Default action for no arguments is allow
     * @var int
     */
    protected $noArgumentsDefaultAction = Acl::ALLOW;

    /**
     * Company Object
     *
     * @var Companies
     */
    protected $company;

    /**
     * App Objc
     *
     * @var Apps
     */
    protected $app;

      /**
     * Class constructor.
     *
     * @param  array $options Adapter config
     * @throws Exception
     */
    public function __construct(array $options)
    {
        if (!isset($options['db']) || !$options['db'] instanceof DbAdapter) {
            throw new Exception(
                'Parameter "db" is required and it must be an instance of Phalcon\Acl\AdapterInterface'
            );
        }

        $this->connection = $options['db'];

        foreach (['roles', 'resources', 'resourcesAccesses', 'accessList', 'rolesInherits'] as $table) {
            if (!isset($options[$table]) || empty($options[$table]) || !is_string($options[$table])) {
                throw new Exception("Parameter '{$table}' is required and it must be a non empty string");
            }

            $this->{$table} = $this->connection->escapeIdentifier($options[$table]);
        }
    }

    /**
     * Set current user Company
     *
     * @param Companies $company
     * @return void
     */
    public function setCompany(Companies $company): void
    {
        $this->company = $company;
    }

    /**
     * Set current user app
     *
     * @param Apps $app
     * @return void
     */
    public function setApp(Apps $app): void
    {
        $this->app = $app;
    }

    /**
     * Get the current App
     *
     * @return void
     */
    public function getApp(): Apps
    {
        if (!is_object($this->app)) {
            $this->app = new Apps();
            $this->app->id = 0;
            $this->app->name = 'Canvas';
        }

        return $this->app;
    }

    /**
     * {@inheritdoc}
     *
     * Example:
     * <code>
     * $acl->addRole(new Phalcon\Acl\Role('administrator'), 'consultor');
     * $acl->addRole('administrator', 'consultor');
     * </code>
     *
     * @param  \Phalcon\Acl\Role|string $role
     * @param  string                   $accessInherits
     * @return boolean
     * @throws \Phalcon\Acl\Exception
     */
    public function addRole($role, $accessInherits = null): bool
    {
        if (is_string($role)) {
            $role = new Role($role, ucwords($role) . ' Role');
        }
        if (!$role instanceof RoleInterface) {
            throw new Exception('Role must be either an string or implement RoleInterface');
        }

        $exists = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM {$this->roles} WHERE name = ?",
            null,
            [$role->getName()]
        );

        if (!$exists[0]) {
            $this->connection->execute(
                "INSERT INTO {$this->roles} (name, description, apps_id, created_at) VALUES (?, ?, ?, ?)",
                [$role->getName(), $role->getDescription(), $this->getApp()->getId(), date('Y-m-d H:i:s')]
            );
            $this->connection->execute(
                "INSERT INTO {$this->accessList} (roles_name, resources_name, access_name, allowed, apps_id, created_at) VALUES (?, ?, ?, ?, ?, ?)",
                [$role->getName(), '*', '*', $this->_defaultAccess, $this->getApp()->getId(), date('Y-m-d H:i:s')]
            );
        }
        if ($accessInherits) {
            return $this->addInherit($role->getName(), $accessInherits);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $roleName
     * @param  string $roleToInherit
     * @throws \Phalcon\Acl\Exception
     */
    public function addInherit($roleName, $roleToInherit): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->roles} WHERE name = ?";
        $exists = $this->connection->fetchOne($sql, null, [$roleName]);
        if (!$exists[0]) {
            throw new Exception("Role '{$roleName}' does not exist in the role list");
        }
        $exists = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM {$this->rolesInherits} WHERE roles_name = ? AND roles_inherit = ?",
            null,
            [$roleName, $roleToInherit]
        );
        if (!$exists[0]) {
            $this->connection->execute(
                "INSERT INTO {$this->rolesInherits} VALUES (?, ?)",
                [$roleName, $roleToInherit]
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $roleName
     * @return boolean
     */
    public function isRole($roleName): bool
    {
        $exists = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM {$this->roles} WHERE name = ?",
            null,
            [$roleName]
        );
        return (bool) $exists[0];
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $resourceName
     * @return boolean
     */
    public function isResource($resourceName): bool
    {
        $exists = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM {$this->resources} WHERE name = ?",
            null,
            [$resourceName]
        );
        return (bool) $exists[0];
    }

    /**
     * {@inheritdoc}
     * Example:
     * <code>
     * //Add a resource to the the list allowing access to an action
     * $acl->addResource(new Phalcon\Acl\Resource('customers'), 'search');
     * $acl->addResource('customers', 'search');
     * //Add a resource  with an access list
     * $acl->addResource(new Phalcon\Acl\Resource('customers'), ['create', 'search']);
     * $acl->addResource('customers', ['create', 'search']);
     * $acl->addResource('App.customers', ['create', 'search']);
     * </code>
     *
     * @param  \Phalcon\Acl\Resource|string $resource
     * @param  array|string                 $accessList
     * @return boolean
     */
    public function addResource($resource, $accessList = null): bool
    {
        if (!is_object($resource)) {
            //echeck if we have a dot , taht means we are sending the specific app to use
            if (strpos($resource, '.') !== false) {
                $appResource = explode('.', $resource);
                $resource = $appResource[1];
                $appName = $appResource[0];

                //look for the app and set it
                if ($app = Apps::findFirstByName($appName)) {
                    $this->setApp($app);
                }
            }

            $resource = new Resource($resource);
        }

        $exists = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM {$this->resources} WHERE name = ?",
            null,
            [$resource->getName()]
        );

        if (!$exists[0]) {
            $this->connection->execute(
                "INSERT INTO {$this->resources} (name, description, apps_id, created_at) VALUES (?, ?, ?, ?)",
                [$resource->getName(), $resource->getDescription(), $this->getApp()->getId(), date('Y-m-d H:i:s')]
            );
        }

        if ($accessList) {
            return $this->addResourceAccess($resource->getName(), $accessList);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string       $resourceName
     * @param  array|string $accessList
     * @return boolean
     * @throws \Phalcon\Acl\Exception
     */
    public function addResourceAccess($resourceName, $accessList): bool
    {
        if (!$this->isResource($resourceName)) {
            throw new Exception("Resource '{$resourceName}' does not exist in ACL");
        }

        $sql = "SELECT COUNT(*) FROM {$this->resourcesAccesses} WHERE resources_name = ? AND access_name = ? AND apps_id = ?";
        if (!is_array($accessList)) {
            $accessList = [$accessList];
        }

        foreach ($accessList as $accessName) {
            $exists = $this->connection->fetchOne($sql, null, [$resourceName, $accessName, $this->getApp()->getId()]);
            if (!$exists[0]) {
                $this->connection->execute(
                    'INSERT INTO ' . $this->resourcesAccesses . ' (resources_name, access_name, apps_id, created_at) VALUES (?, ?, ?, ?)',
                    [$resourceName, $accessName, $this->getApp()->getId(), date('Y-m-d H:i:s')]
                );
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Phalcon\Acl\Resource[]
     */
    public function getResources(): \Phalcon\Acl\ResourceInterface
    {
        $resources = [];
        $sql = "SELECT * FROM {$this->resources}";
        foreach ($this->connection->fetchAll($sql, Db::FETCH_ASSOC) as $row) {
            $resources[] = new Resource($row['name'], $row['description']);
        }
        return $resources;
    }

    /**
     * {@inheritdoc}
     *
     * @return RoleInterface[]
     */
    public function getRoles(): \Phalcon\Acl\RoleInterface
    {
        $roles = [];
        $sql = "SELECT * FROM {$this->roles}";
        foreach ($this->connection->fetchAll($sql, Db::FETCH_ASSOC) as $row) {
            $roles[] = new Role($row['name'], $row['description']);
        }
        return $roles;
    }

    /**
     * {@inheritdoc}
     *
     * @param string       $resourceName
     * @param array|string $accessList
     */
    public function dropResourceAccess($resourceName, $accessList)
    {
        throw new BadMethodCallException('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     * You can use '*' as wildcard
     * Example:
     * <code>
     * //Allow access to guests to search on customers
     * $acl->allow('guests', 'customers', 'search');
     * //Allow access to guests to search or create on customers
     * $acl->allow('guests', 'customers', ['search', 'create']);
     * //Allow access to any role to browse on products
     * $acl->allow('*', 'products', 'browse');
     * //Allow access to any role to browse on any resource
     * $acl->allow('*', '*', 'browse');
     * </code>
     *
     * @param string       $roleName
     * @param string       $resourceName
     * @param array|string $access
     * @param mixed $func
     */
    public function allow($roleName, $resourceName, $access, $func = null)
    {
        $this->allowOrDeny($roleName, $resourceName, $access, Acl::ALLOW);
    }

    /**
     * {@inheritdoc}
     * You can use '*' as wildcard
     * Example:
     * <code>
     * //Deny access to guests to search on customers
     * $acl->deny('guests', 'customers', 'search');
     * //Deny access to guests to search or create on customers
     * $acl->deny('guests', 'customers', ['search', 'create']);
     * //Deny access to any role to browse on products
     * $acl->deny('*', 'products', 'browse');
     * //Deny access to any role to browse on any resource
     * $acl->deny('*', '*', 'browse');
     * </code>
     *
     * @param  string       $roleName
     * @param  string       $resourceName
     * @param  array|string $access
     * @param  mixed $func
     * @return boolean
     */
    public function deny($roleName, $resourceName, $access, $func = null)
    {
        $this->allowOrDeny($roleName, $resourceName, $access, Acl::DENY);
    }

    /**
     * {@inheritdoc}
     * Example:
     * <code>
     * //Does Andres have access to the customers resource to create?
     * $acl->isAllowed('Andres', 'Products', 'create');
     * //Do guests have access to any resource to edit?
     * $acl->isAllowed('guests', '*', 'edit');
     * </code>
     *
     * @param string $role
     * @param string $resource
     * @param string $access
     * @param array  $parameters
     * @return bool
     */
    public function isAllowed($role, $resource, $access, array $parameters = null): bool
    {
        $sql = implode(' ', [
            'SELECT ' . $this->connection->escapeIdentifier('allowed') . " FROM {$this->accessList} AS a",
            // role_name in:
            'WHERE roles_name IN (',
                // given 'role'-parameter
            'SELECT ? ',
                // inherited role_names
            "UNION SELECT roles_inherit FROM {$this->rolesInherits} WHERE roles_name = ?",
                // or 'any'
            "UNION SELECT '*'",
            ')',
            // resources_name should be given one or 'any'
            "AND resources_name IN (?, '*')",
            // access_name should be given one or 'any'
            "AND access_name IN (?, '*')",
            'AND apps_id = ? ',
            // order be the sum of bools for 'literals' before 'any'
            'ORDER BY ' . $this->connection->escapeIdentifier('allowed') . ' DESC',
            // get only one...
            'LIMIT 1'
        ]);
        // fetch one entry...
        $allowed = $this->connection->fetchOne($sql, Db::FETCH_NUM, [$role, $role, $resource, $access, $this->getApp()->getId()]);
        if (is_array($allowed)) {
            return (bool) $allowed[0];
        }
        /**
         * Return the default access action
         */
        return $this->_defaultAccess;
    }

    /**
     * Returns the default ACL access level for no arguments provided
     * in isAllowed action if there exists func for accessKey
     *
     * @return int
     */
    public function getNoArgumentsDefaultAction(): int
    {
        return $this->noArgumentsDefaultAction;
    }

    /**
     * Sets the default access level for no arguments provided
     * in isAllowed action if there exists func for accessKey
     *
     * @param int $defaultAccess Phalcon\Acl::ALLOW or Phalcon\Acl::DENY
     */
    public function setNoArgumentsDefaultAction($defaultAccess)
    {
        $this->noArgumentsDefaultAction = intval($defaultAccess);
    }

    /**
     * Inserts/Updates a permission in the access list
     *
     * @param  string  $roleName
     * @param  string  $resourceName
     * @param  string  $accessName
     * @param  integer $action
     * @return boolean
     * @throws \Phalcon\Acl\Exception
     */
    protected function insertOrUpdateAccess($roleName, $resourceName, $accessName, $action)
    {
        /**
         * Check if the access is valid in the resource unless wildcard
         */
        if ($resourceName !== '*' && $accessName !== '*') {
            $sql = "SELECT COUNT(*) FROM {$this->resourcesAccesses} WHERE resources_name = ? AND access_name = ?";
            $exists = $this->connection->fetchOne($sql, null, [$resourceName, $accessName]);
            if (!$exists[0]) {
                throw new Exception(
                    "Access '{$accessName}' does not exist in resource '{$resourceName}' in ACL"
                );
            }
        }
        /**
         * Update the access in access_list
         */
        $sql = "SELECT COUNT(*) FROM {$this->accessList} "
            . ' WHERE roles_name = ? AND resources_name = ? AND access_name = ? AND apps_id = ?';
        $exists = $this->connection->fetchOne($sql, null, [$roleName, $resourceName, $accessName, $this->getApp()->getId()]);
        if (!$exists[0]) {
            $sql = "INSERT INTO {$this->accessList} (roles_name, resources_name, access_name, allowed, apps_id) VALUES (?, ?, ?, ?, ?)";
            $params = [$roleName, $resourceName, $accessName, $action, $this->getApp()->getId()];
        } else {
            $sql = "UPDATE {$this->accessList} SET allowed = ? " .
                'WHERE roles_name = ? AND resources_name = ? AND access_name = ? AND apps_id = ?';
            $params = [$action, $roleName, $resourceName, $accessName, $this->getApp()->getId()];
        }
        $this->connection->execute($sql, $params);

        /**
         * Update the access '*' in access_list
         */
        $sql = "SELECT COUNT(*) FROM {$this->accessList} " .
            'WHERE roles_name = ? AND resources_name = ? AND access_name = ? and apps_id = ?';
        $exists = $this->connection->fetchOne($sql, null, [$roleName, $resourceName, '*', $this->getApp()->getId()]);
        if (!$exists[0]) {
            $sql = "INSERT INTO {$this->accessList} (roles_name, resources_name, access_name, allowed, apps_id) VALUES (?, ?, ?, ?, ?)";
            $this->connection->execute($sql, [$roleName, $resourceName, '*', $this->_defaultAccess, $this->getApp()->getId()]);
        }
        return true;
    }

    /**
     * Inserts/Updates a permission in the access list
     *
     * @param  string       $roleName
     * @param  string       $resourceName
     * @param  array|string $access
     * @param  integer      $action
     * @throws \Phalcon\Acl\Exception
     */
    protected function allowOrDeny($roleName, $resourceName, $access, $action)
    {
        if (!$this->isRole($roleName)) {
            throw new Exception("Role '{$roleName}' does not exist in the list");
        }
        if (!is_array($access)) {
            $access = [$access];
        }
        foreach ($access as $accessName) {
            $this->insertOrUpdateAccess($roleName, $resourceName, $accessName, $action);
        }
    }
}

<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddRolesIdToRoleTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table("user_roles");
        $table->addColumn('roles_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'users_id'])->save();
        $this->table("user_roles")->changeColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'roles_id'])->update();
        $this->table("user_roles")->changeColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'apps_id'])->update();
        $this->table("user_roles")->changeColumn('created_at', 'datetime', ['null' => false, 'after' => 'company_id'])->update();
        $this->table("user_roles")->changeColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->update();
        $this->table("user_roles")->changeColumn('is_deleted', 'boolean', ['null' => true, 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->update();
        $table->save();
    }
}

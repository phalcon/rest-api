<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddAcl extends AbstractMigration
{
    public function change()
    {
        $table = $this->table("access_list", ['id' => false, 'primary_key' => ["roles_name", "resources_name", "access_name"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('roles_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4"])->save();
        $table->addColumn('resources_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'roles_name'])->save();
        $table->addColumn('access_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'resources_name'])->save();
        $table->addColumn('allowed', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'access_name'])->save();
        $table->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'allowed'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();
        $table = $this->table("resources", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->save();
        $table->addColumn('name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'id'])->save();
        $table->addColumn('description', 'text', ['null' => true, 'limit' => 65535, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'name'])->save();
        $table->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'description'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();
        $table = $this->table("resources_accesses", ['id' => false, 'primary_key' => ["resources_name", "access_name"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('resources_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4"])->save();
        $table->addColumn('access_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'resources_name'])->save();
        $table->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'access_name'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => false, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();
        $table = $this->table("roles", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->save();
        $table->addColumn('name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'id'])->save();
        $table->addColumn('description', 'text', ['null' => true, 'limit' => 65535, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'name'])->save();
        $table->addColumn('scope', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'description'])->save();
        $table->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'scope'])->save();
        $table->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_id'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();
        $table = $this->table("roles_inherits", ['id' => false, 'primary_key' => ["roles_name", "roles_inherit"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('roles_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4"])->save();
        $table->addColumn('roles_inherit', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'roles_name'])->save();
        $table->save();
    }
}

<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddAcl extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('access_list', ['id' => false, 'primary_key' => ['roles_name', 'resources_name', 'access_name'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_520_ci', 'comment' => '', 'row_format' => 'Dynamic']);
        $table->addColumn('roles_name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4'])
            ->addColumn('resources_name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4', 'after' => 'roles_name'])
            ->addColumn('access_name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4', 'after' => 'resources_name'])
            ->addColumn('allowed', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'access_name'])
            ->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'allowed'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])
            ->save();

        $table = $this->table('resources', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_520_ci', 'comment' => '', 'row_format' => 'Dynamic']);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])
            ->addColumn('name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4', 'after' => 'id'])
            ->addColumn('description', 'text', ['null' => true, 'limit' => 65535, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4', 'after' => 'name'])
            ->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'description'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])
            ->save();

        $table = $this->table('resources_accesses', ['id' => false, 'primary_key' => ['resources_name', 'access_name'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_520_ci', 'comment' => '', 'row_format' => 'Dynamic']);
        $table->addColumn('resources_name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4'])
            ->addColumn('access_name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4', 'after' => 'resources_name'])
            ->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'access_name'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])
            ->addColumn('updated_at', 'datetime', ['null' => false, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])
            ->save();

        $table = $this->table('roles', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_520_ci', 'comment' => '', 'row_format' => 'Dynamic']);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])
            ->addColumn('name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4', 'after' => 'id'])
            ->addColumn('description', 'text', ['null' => true, 'limit' => 65535, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4', 'after' => 'name'])
            ->addColumn('scope', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'description'])
            ->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'scope'])
            ->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_id'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])
            ->save();

        //add default languages
        $data = [
            [
                'name' => 'Admins',
                'description' => 'System Administrator',
                'scope' => 0,
                'company_id' => 0,
                'apps_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ], [
                'name' => 'Users',
                'description' => 'Normal Users can (CRUD)',
                'scope' => 0,
                'company_id' => 0,
                'apps_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ], [
                'name' => 'Agents',
                'description' => 'Agents Users can (CRU)',
                'scope' => 0,
                'company_id' => 0,
                'apps_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ]
        ];

        $table = $this->table('roles');
        $table->insert($data)->save();

        $table = $this->table('roles_inherits', ['id' => false, 'primary_key' => ['roles_name', 'roles_inherit'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_520_ci', 'comment' => '', 'row_format' => 'Dynamic']);
        $table->addColumn('roles_name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4'])
            ->addColumn('roles_inherit', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_520_ci', 'encoding' => 'utf8mb4', 'after' => 'roles_name'])
            ->save();
    }
}

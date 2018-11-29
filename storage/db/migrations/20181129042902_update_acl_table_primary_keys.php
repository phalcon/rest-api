<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class UpdateAclTablePrimaryKeys extends AbstractMigration
{
    public function change()
    {
        $this->table("apps")->changeColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'signed' => false, 'identity' => 'enable'])->update();
        $this->table("access_list")->changeColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'allowed'])->update();
        $this->table("resources_accesses")->changeColumn('created_at', 'datetime', ['null' => false, 'after' => 'apps_id'])->update();
    }
}

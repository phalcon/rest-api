<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class UpdateAclTablePrimaryKeys extends AbstractMigration
{
    public function change()
    {
        $this->table('apps')->changeColumn('id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'signed' => false, 'identity' => 'enable'])->update();
        $this->table('access_list')->changeColumn('apps_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'allowed'])->update();
    }
}

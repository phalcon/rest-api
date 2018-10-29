<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddUserIsDeletedColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table("users");
        $table->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])->save();
        $table->save();
    }
}

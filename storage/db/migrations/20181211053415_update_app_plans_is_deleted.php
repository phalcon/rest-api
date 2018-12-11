<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class UpdateAppPlansIsDeleted extends AbstractMigration
{
    public function change()
    {
        $this->table("apps_plans")->changeColumn('is_deleted', 'integer', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->update();
    }
}

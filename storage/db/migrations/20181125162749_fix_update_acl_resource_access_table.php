<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixUpdateAclResourceAccessTable extends AbstractMigration
{
    public function change()
    {
        $this->table("resources_accesses")->changeColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->update();
    }
}

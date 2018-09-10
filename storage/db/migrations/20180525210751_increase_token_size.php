<?php


use Phinx\Migration\AbstractMigration;

class IncreaseTokenSize extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_users');
        $table
            ->changeColumn(
                'usr_token',
                'string',
                [
                    'limit'   => 256,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_users');
        $table
            ->changeColumn(
                'usr_token',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->save();
    }
}

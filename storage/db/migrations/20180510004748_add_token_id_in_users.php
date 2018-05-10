<?php


use Phinx\Migration\AbstractMigration;

class AddTokenIdInUsers extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_users');
        $table
            ->addColumn(
                'usr_token_id',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addIndex('usr_token_id')
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_users');
        $table
            ->removeColumn('usr_token_id')
            ->save();
    }
}

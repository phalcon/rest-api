<?php


use Phinx\Migration\AbstractMigration;

class AddTokenAndAudienceFieldsToUsers extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_users');
        $table
            ->addColumn(
                'usr_domain_name',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'usr_token',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addIndex('usr_token')
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_users');
        $table
            ->removeColumn('usr_domain_name')
            ->removeColumn('usr_token')
            ->save();
    }
}

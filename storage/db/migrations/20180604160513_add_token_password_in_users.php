<?php


use Phinx\Migration\AbstractMigration;

class AddTokenPasswordInUsers extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_users');
        $table
            ->addColumn(
                'usr_token_password',
                'string',
                [
                    'limit'   => 64,
                    'null'    => false,
                    'default' => '',
                    'after'   => 'usr_domain_name',
                ]
            )
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_users');
        $table
            ->removeColumn('usr_token_password')
            ->save();
    }
}

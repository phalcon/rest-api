<?php


use Phinx\Migration\AbstractMigration;

class RemoveTokenFieldsFromUsers extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_users');
        $table
            ->removeColumn('usr_token_pre')
            ->removeColumn('usr_token_mid')
            ->removeColumn('usr_token_post')
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_users');
        $table
            ->addColumn(
                'usr_token_pre',
                'string',
                [
                    'limit'   => 256,
                    'null'    => false,
                    'default' => '',
                    'after'   => 'usr_domain_name',
                ]
            )
            ->addColumn(
                'usr_token_mid',
                'string',
                [
                    'limit'   => 256,
                    'null'    => false,
                    'default' => '',
                    'after'   => 'usr_token_pre',
                ]
            )
            ->addColumn(
                'usr_token_post',
                'string',
                [
                    'limit'   => 256,
                    'null'    => false,
                    'default' => '',
                    'after'   => 'usr_token_mid',
                ]
            )
            ->addIndex('usr_token_pre')
            ->addIndex('usr_token_mid')
            ->addIndex('usr_token_post')
            ->save();
    }
}

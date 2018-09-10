<?php


use Phinx\Migration\AbstractMigration;

class SplitTokenField extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_users');
        $table
            ->renameColumn('usr_token', 'usr_token_pre')
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
            ->addIndex('usr_token_mid')
            ->addIndex('usr_token_post')
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_users');
        $table
            ->renameColumn('usr_token_pre', 'usr_token')
            ->removeColumn('usr_token_mid')
            ->removeColumn('usr_token_post')
            ->save();
    }
}

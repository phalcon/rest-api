<?php


use Phinx\Migration\AbstractMigration;

class AddUsersTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table(
            'co_users',
            [
                'id'     => 'usr_id',
                'signed' => false,
            ]
        );

        $table
            ->addColumn(
                'usr_status_flag',
                'boolean',
                [
                    'signed'  => false,
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addColumn(
                'usr_username',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'usr_password',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addIndex('usr_status_flag')
            ->addIndex('usr_username')
            ->addIndex('usr_password')
            ->save();

        $this->execute(
            'ALTER TABLE co_users ' .
            'CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    public function down()
    {
        $this->dropTable('co_users');
    }
}

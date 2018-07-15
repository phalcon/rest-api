<?php


use Phinx\Migration\AbstractMigration;

class AddCompaniesTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table(
            'co_companies',
            [
                'id'     => 'com_id',
                'signed' => false,
            ]
        );

        $table
            ->addColumn(
                'com_name',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_address',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_city',
                'string',
                [
                    'limit'   => 64,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_telephone',
                'string',
                [
                    'limit'   => 24,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addIndex('com_name')
            ->addIndex('com_city')
            ->save();

        $this->execute(
            'ALTER TABLE co_companies ' .
            'CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    public function down()
    {
        $this->dropTable('co_companies');
    }
}

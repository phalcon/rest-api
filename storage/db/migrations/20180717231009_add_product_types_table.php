<?php


use Phinx\Migration\AbstractMigration;

class AddProductTypesTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table(
            'co_product_types',
            [
                'id'     => 'prt_id',
                'signed' => false,
            ]
        );

        $table
            ->addColumn(
                'prt_name',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'prt_description',
                'string',
                [
                    'limit'   => 256,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addIndex('prt_name')
            ->save();

        $this->execute(
            'ALTER TABLE co_product_types ' .
            'CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    public function down()
    {
        $this->dropTable('co_product_types');
    }
}

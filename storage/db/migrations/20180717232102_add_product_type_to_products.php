<?php


use Phinx\Migration\AbstractMigration;

class AddProductTypeToProducts extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_products');

        $table
            ->addColumn(
                'prd_prt_id',
                'integer',
                [
                    'signed'  => false,
                    'limit'   => 11,
                    'null'    => false,
                    'default' => 0,
                    'after'   => 'prd_id',
                ]
            )
            ->addIndex('prd_id')
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_products');
        $table->removeColumn('prd_prt_id');
    }
}

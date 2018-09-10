<?php


use Phinx\Migration\AbstractMigration;

class AddCompaniesToProductsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table(
            'co_companies_x_products',
            [
                'id'          => false,
                'primary_key' => [
                    'cxp_com_id',
                    'cxp_prd_id',
                ],
            ]
        );

        $table
            ->addColumn(
                'cxp_com_id',
                'integer',
                [
                    'signed'  => false,
                    'limit'   => 11,
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addColumn(
                'cxp_prd_id',
                'integer',
                [
                    'signed'  => false,
                    'limit'   => 11,
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addIndex('cxp_com_id')
            ->addIndex('cxp_prd_id')
            ->save();

        $this->execute(
            'ALTER TABLE co_companies_x_products ' .
            'CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    public function down()
    {
        $this->dropTable('co_companies_x_products');
    }
}

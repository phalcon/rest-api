<?php


use Phinx\Migration\AbstractMigration;

class AddIndividualsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table(
            'co_individuals',
            [
                'id'     => 'ind_id',
                'signed' => false,
            ]
        );
        $table
            ->addColumn(
                'ind_com_id',
                'integer',
                [
                    'signed'  => false,
                    'limit'   => 11,
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addColumn(
                'ind_idt_id',
                'integer',
                [
                    'signed'  => false,
                    'limit'   => 11,
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addColumn(
                'ind_name_prefix',
                'string',
                [
                    'limit'   => 16,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'ind_name_first',
                'string',
                [
                    'limit'   => 64,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'ind_name_middle',
                'string',
                [
                    'limit'   => 64,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'ind_name_last',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'ind_name_suffix',
                'string',
                [
                    'limit'   => 16,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addIndex('ind_com_id')
            ->addIndex('ind_idt_id')
            ->addIndex('ind_name_first')
            ->addIndex('ind_name_middle')
            ->addIndex('ind_name_last')
            ->save();

        $this->execute(
            'ALTER TABLE co_individuals ' .
            'CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    public function down()
    {
        $this->dropTable('co_employees');
    }
}

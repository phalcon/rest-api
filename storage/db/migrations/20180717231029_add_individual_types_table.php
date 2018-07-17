<?php


use Phinx\Migration\AbstractMigration;

class AddIndividualTypesTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table(
            'co_individual_types',
            [
                'id'     => 'idt_id',
                'signed' => false,
            ]
        );

        $table
            ->addColumn(
                'idt_name',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'idt_description',
                'string',
                [
                    'limit'   => 256,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addIndex('idt_name')
            ->save();

        $this->execute(
            'ALTER TABLE co_individual_types ' .
            'CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    public function down()
    {
        $this->dropTable('co_individual_types');
    }
}

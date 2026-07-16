<?php


use Phinx\Migration\AbstractMigration;

class RenameDomainToIssuer extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_users');
        $table
            ->renameColumn('usr_domain_name', 'usr_issuer')
            ->save();

        $table
            ->addIndex('usr_issuer')
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_users');
        $table
            ->removeIndex('usr_issuer')
            ->save();

        $table
            ->renameColumn('usr_issuer', 'usr_domain_name')
            ->save();
    }
}

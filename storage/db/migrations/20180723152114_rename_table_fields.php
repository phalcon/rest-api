<?php


use Phinx\Migration\AbstractMigration;

class RenameTableFields extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('co_companies');
        $table
            ->renameColumn('com_id', 'id')
            ->renameColumn('com_name', 'name')
            ->renameColumn('com_address', 'address')
            ->renameColumn('com_city', 'city')
            ->renameColumn('com_telephone', 'phone')
            ->save();

        $table = $this->table('co_companies_x_products');
        $table
            ->renameColumn('cxp_com_id', 'companyId')
            ->renameColumn('cxp_prd_id', 'productId')
            ->save();

        $table = $this->table('co_individual_types');
        $table
            ->renameColumn('idt_id', 'id')
            ->renameColumn('idt_name', 'name')
            ->renameColumn('idt_description', 'description')
            ->save();

        $table = $this->table('co_individuals');
        $table
            ->renameColumn('ind_id', 'id')
            ->renameColumn('ind_com_id', 'companyId')
            ->renameColumn('ind_idt_id', 'typeId')
            ->renameColumn('ind_name_prefix', 'prefix')
            ->renameColumn('ind_name_first', 'first')
            ->renameColumn('ind_name_middle', 'middle')
            ->renameColumn('ind_name_last', 'last')
            ->renameColumn('ind_name_suffix', 'suffix')
            ->save();

        $table = $this->table('co_product_types');
        $table
            ->renameColumn('prt_id', 'id')
            ->renameColumn('prt_name', 'name')
            ->renameColumn('prt_description', 'description')
            ->save();

        $table = $this->table('co_products');
        $table
            ->renameColumn('prd_id', 'id')
            ->renameColumn('prd_prt_id', 'typeId')
            ->renameColumn('prd_name', 'name')
            ->renameColumn('prd_description', 'description')
            ->renameColumn('prd_quantity', 'quantity')
            ->renameColumn('prd_price', 'price')
            ->save();

        $table = $this->table('co_users');
        $table
            ->renameColumn('usr_id', 'id')
            ->renameColumn('usr_status_flag', 'status')
            ->renameColumn('usr_username', 'username')
            ->renameColumn('usr_password', 'password')
            ->renameColumn('usr_issuer', 'issuer')
            ->renameColumn('usr_token_password', 'tokenPassword')
            ->renameColumn('usr_token_id', 'tokenId')
            ->save();
    }

    public function down()
    {
        $table = $this->table('co_companies');
        $table
            ->renameColumn('id', 'com_id')
            ->renameColumn('name', 'com_name')
            ->renameColumn('address', 'com_address')
            ->renameColumn('city', 'com_city')
            ->renameColumn('phone', 'com_telephone')
            ->save();

        $table = $this->table('co_companies_x_products');
        $table
            ->renameColumn('companyId', 'cxp_com_id')
            ->renameColumn('productId', 'cxp_prd_id')
            ->save();

        $table = $this->table('co_individual_types');
        $table
            ->renameColumn('id', 'idt_id')
            ->renameColumn('name', 'idt_name')
            ->renameColumn('description', 'idt_description')
            ->save();

        $table = $this->table('co_individuals');
        $table
            ->renameColumn('id', 'ind_id')
            ->renameColumn('companyId', 'ind_com_id')
            ->renameColumn('typeId', 'ind_idt_id')
            ->renameColumn('prefix', 'ind_name_prefix')
            ->renameColumn('first', 'ind_name_first')
            ->renameColumn('middle', 'ind_name_middle')
            ->renameColumn('last', 'ind_name_last')
            ->renameColumn('suffix', 'ind_name_suffix')
            ->save();

        $table = $this->table('co_product_types');
        $table
            ->renameColumn('id', 'prt_id')
            ->renameColumn('name', 'prt_name')
            ->renameColumn('description', 'prt_description')
            ->save();

        $table = $this->table('co_products');
        $table
            ->renameColumn('id', 'prd_id')
            ->renameColumn('typeId', 'prd_prt_id')
            ->renameColumn('name', 'prd_name')
            ->renameColumn('description', 'prd_description')
            ->renameColumn('quantity', 'prd_quantity')
            ->renameColumn('price', 'prd_price')
            ->save();

        $table = $this->table('co_users');
        $table
            ->renameColumn('id', 'usr_id')
            ->renameColumn('status', 'usr_status_flag')
            ->renameColumn('username', 'usr_username')
            ->renameColumn('password', 'usr_password')
            ->renameColumn('issuer', 'usr_issuer')
            ->renameColumn('tokenPassword', 'usr_token_password')
            ->renameColumn('tokenId', 'usr_token_id')
            ->save();
    }
}

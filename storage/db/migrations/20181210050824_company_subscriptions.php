<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CompanySubscriptions extends AbstractMigration
{
    public function change()
    {
        $table = $this->table("user_company_apps");
        $table->addColumn('stripe_id', 'string', ['null' => true, 'limit' => 50, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'apps_id'])->save();
        $table->addColumn('subscriptions_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'stripe_id'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'subscriptions_id'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();
        $table = $this->table("apps_plans");
        $table->addColumn('is_default', 'integer', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'free_trial_dates'])->save();
        $this->table("apps_plans")->changeColumn('created_at', 'date', ['null' => true, 'after' => 'is_default'])->update();
        $this->table("apps_plans")->changeColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->update();
        $this->table("apps_plans")->changeColumn('is_deleted', 'blob', ['null' => true, 'limit' => MysqlAdapter::BLOB_TINY, 'after' => 'updated_at'])->update();
        $table->save();
    }
}

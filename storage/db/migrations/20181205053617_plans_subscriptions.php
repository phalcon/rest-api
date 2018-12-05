<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class PlansSubscriptions extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER DATABASE CHARACTER SET 'utf8';");
        $this->execute("ALTER DATABASE COLLATE='utf8_general_mysql500_ci';");
        $this->table("apps")->changeColumn('name', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'id'])->update();
        $this->table("apps")->changeColumn('description', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'name'])->update();
       
        $table = $this->table("apps");
        
        $table->addColumn('url', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'description'])->save();
        $table->addColumn('is_actived', 'boolean', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'url'])->save();
        $this->table("apps")->changeColumn('created_at', 'datetime', ['null' => true, 'after' => 'is_actived'])->update();
        $this->table("apps")->changeColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->update();
        $this->table("apps")->changeColumn('is_deleted', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])->update();
        $table->save();
       
        $this->table("apps_roles")->changeColumn('roles_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'apps_id'])->update();
        $this->table("companies")->changeColumn('name', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'id'])->update();
        $this->table("companies")->changeColumn('profile_image', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'name'])->update();
        $this->table("companies")->changeColumn('website', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'profile_image'])->update();
        $this->table("company_settings")->changeColumn('name', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'company_id'])->update();
        $this->table("company_settings")->changeColumn('value', 'text', ['null' => false, 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'name'])->update();
        $this->table("languages")->changeColumn('id', 'string', ['null' => false, 'limit' => 2, 'collation' => "utf8_general_ci", 'encoding' => "utf8"])->update();
        $this->table("languages")->changeColumn('name', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'id'])->update();
        $this->table("languages")->changeColumn('title', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'name'])->update();
        $this->table("session_keys")->changeColumn('sessions_id', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8"])->update();
        $this->table("session_keys")->changeColumn('last_ip', 'string', ['null' => true, 'limit' => 39, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'users_id'])->update();
        $this->table("sessions")->changeColumn('id', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8"])->update();
        $this->table("sessions")->changeColumn('token', 'text', ['null' => false, 'limit' => 65535, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'users_id'])->update();
        $this->table("sessions")->changeColumn('ip', 'string', ['null' => false, 'limit' => 39, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'time'])->update();
        $this->table("sessions")->changeColumn('page', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'ip'])->update();
        $this->table("sessions")->changeColumn('logged_in', 'enum', ['null' => false, 'default' => "0", 'limit' => 1, 'values' => ['0','1'], 'after' => 'page'])->update();
        $this->table("sessions")->changeColumn('is_admin', 'enum', ['null' => true, 'default' => "0", 'limit' => 1, 'values' => ['0','1'], 'after' => 'logged_in'])->update();
        $this->table("sources")->changeColumn('title', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'id'])->update();
        $this->table("sources")->changeColumn('url', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'title'])->update();
        $this->table("sources")->changeColumn('language_id', 'string', ['null' => true, 'limit' => 5, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'url'])->update();
        $this->table("suscriptions")->changeColumn('stripe_id', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'apps_id'])->update();
        $this->table("suscriptions")->changeColumn('stripe_plan', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'company_id'])->update();
        $this->table("user_config")->changeColumn('name', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'users_id'])->update();
        $this->table("user_config")->changeColumn('value', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'name'])->update();
        $this->table("user_linked_sources")->changeColumn('source_users_id', 'string', ['null' => false, 'limit' => 30, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'source_id'])->update();
        $this->table("user_linked_sources")->changeColumn('source_users_id_text', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'source_users_id'])->update();
        $this->table("user_linked_sources")->changeColumn('source_username', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'source_users_id_text'])->update();
        $this->table("users")->changeColumn('email', 'string', ['null' => false, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'id'])->update();
        $this->table("users")->changeColumn('password', 'string', ['null' => false, 'limit' => 255, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'email'])->update();
        $this->table("users")->changeColumn('firstname', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'password'])->update();
        $this->table("users")->changeColumn('lastname', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'firstname'])->update();
        $this->table("users")->changeColumn('displayname', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'roles_id'])->update();
        $this->table("users")->changeColumn('sex', 'enum', ['null' => false, 'default' => "U", 'limit' => 1, 'values' => ['U','M','F'], 'after' => 'dob'])->update();
        $this->table("users")->changeColumn('timezone', 'string', ['null' => false, 'default' => "America/New_York", 'limit' => 128, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'sex'])->update();
       
        $table = $this->table("users");
        $table->addColumn('default_company_branch', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'default_company'])->save();
        
        $this->table("users")->changeColumn('city_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_MEDIUM, 'precision' => 7, 'signed' => false, 'after' => 'default_company_branch'])->update();
        $this->table("users")->changeColumn('state_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'signed' => false, 'after' => 'city_id'])->update();
        $this->table("users")->changeColumn('country_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_SMALL, 'precision' => 5, 'signed' => false, 'after' => 'state_id'])->update();
        $this->table("users")->changeColumn('profile_privacy', 'enum', ['null' => false, 'default' => "0", 'limit' => 1, 'values' => ['0','1'], 'after' => 'country_id'])->update();
        $this->table("users")->changeColumn('interests', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_MEDIUM, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'profile_privacy'])->update();
        $this->table("users")->changeColumn('profile_image', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'interests'])->update();
        $this->table("users")->changeColumn('profile_remote_image', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'profile_image'])->update();
        $this->table("users")->changeColumn('profile_header', 'string', ['null' => true, 'limit' => 192, 'collation' => "utf8_bin", 'encoding' => "utf8", 'after' => 'profile_remote_image'])->update();
        $this->table("users")->changeColumn('profile_header_mobile', 'string', ['null' => true, 'limit' => 192, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'profile_header'])->update();
        $this->table("users")->changeColumn('user_active', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'profile_header_mobile'])->update();
        $this->table("users")->changeColumn('user_level', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'user_active'])->update();
        $this->table("users")->changeColumn('user_login_tries', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'user_level'])->update();
        $this->table("users")->changeColumn('user_last_login_try', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 19, 'after' => 'user_login_tries'])->update();
        $this->table("users")->changeColumn('session_time', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 19, 'after' => 'user_last_login_try'])->update();
        $this->table("users")->changeColumn('session_page', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'session_time'])->update();
        $this->table("users")->changeColumn('welcome', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'session_page'])->update();
        $this->table("users")->changeColumn('user_activation_key', 'string', ['null' => true, 'limit' => 64, 'collation' => "utf8_bin", 'encoding' => "utf8", 'after' => 'welcome'])->update();
        $this->table("users")->changeColumn('user_activation_email', 'string', ['null' => true, 'limit' => 64, 'collation' => "utf8_bin", 'encoding' => "utf8", 'after' => 'user_activation_key'])->update();
        $this->table("users")->changeColumn('user_activation_forgot', 'string', ['null' => true, 'limit' => 100, 'collation' => "utf8_bin", 'encoding' => "utf8", 'after' => 'user_activation_email'])->update();
        $this->table("users")->changeColumn('language', 'string', ['null' => true, 'limit' => 5, 'collation' => "utf8_bin", 'encoding' => "utf8", 'after' => 'user_activation_forgot'])->update();
        $this->table("users")->changeColumn('karma', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'language'])->update();
        $this->table("users")->changeColumn('votes', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'signed' => false, 'after' => 'karma'])->update();
        $this->table("users")->changeColumn('votes_points', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'votes'])->update();
        $this->table("users")->changeColumn('banned', 'char', ['null' => false, 'default' => "N", 'limit' => 1, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'votes_points'])->update();
        $this->table("users")->changeColumn('created_at', 'datetime', ['null' => true, 'after' => 'banned'])->update();
        $this->table("users")->changeColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->update();
        $this->table("users")->changeColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])->update();
        $table->save();
       
        $this->table("users_associated_company")->changeColumn('identify_id', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'company_id'])->update();
        $this->table("users_associated_company")->changeColumn('user_role', 'string', ['null' => true, 'limit' => 45, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'user_active'])->update();
        
        $table = $this->table("subscriptions", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_unicode_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])
            ->addColumn('user_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'id'])
            ->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'user_id'])
            ->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_id'])
            ->addColumn('name', 'string', ['null' => false, 'limit' => 250, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'apps_id'])
            ->addColumn('stripe_id', 'string', ['null' => false, 'limit' => 250, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'name'])
            ->addColumn('stripe_plan', 'string', ['null' => false, 'limit' => 250, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'stripe_id'])
            ->addColumn('quantity', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'stripe_plan'])
            ->addColumn('trial_ends_at', 'timestamp', ['null' => true, 'after' => 'quantity'])
            ->addColumn('ends_at', 'timestamp', ['null' => true, 'after' => 'trial_ends_at'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'ends_at'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])
            ->save();
       
        $table = $this->table("apps_plans", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_mysql500_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])
            ->addColumn('apps_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'id'])
            ->addColumn('name', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'apps_id'])
            ->addColumn('description', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'name'])
            ->addColumn('stripe_id', 'string', ['null' => true, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'description'])
            ->addColumn('stripe_plan', 'string', ['null' => true, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'stripe_id'])
            ->addColumn('pricing', 'decimal', ['null' => true, 'precision' => 10, 'scale' => 2, 'after' => 'stripe_plan'])
            ->addColumn('currency_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'pricing'])
            ->addColumn('free_trial_dates', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'currency_id'])
            ->addColumn('created_at', 'date', ['null' => true, 'after' => 'free_trial_dates'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'blob', ['null' => true, 'limit' => MysqlAdapter::BLOB_TINY, 'after' => 'updated_at'])
            ->save();

        $table = $this->table("apps_plans_settings", ['id' => false, 'primary_key' => ["apps_plans_id", "apps_id", "key"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_mysql500_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('apps_plans_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10])
            ->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'apps_plans_id'])
            ->addColumn('key', 'string', ['null' => false, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'apps_id'])
            ->addColumn('value', 'string', ['null' => false, 'limit' => 255, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'key'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'value'])
            ->addColumn('updadate_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'boolean', ['null' => true, 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updadate_at'])
            ->save();
        
        $table = $this->table("apps_plans_settings");
        if ($table->hasIndex("appskeys")) {
            $table->removeIndexByName("appskeys")->save();
        }
        $table = $this->table("apps_plans_settings");
        $table->addIndex(['apps_plans_id','key'], ['name' => "appskeys", 'unique' => true])->save();
        $table = $this->table("apps_plans_settings");
        if ($table->hasIndex("appkey")) {
            $table->removeIndexByName("appkey")->save();
        }
        $table = $this->table("apps_plans_settings");
        $table->addIndex(['key'], ['name' => "appkey", 'unique' => false])->save();
        $table = $this->table("apps_plans_settings");
        if ($table->hasIndex("plansapps")) {
            $table->removeIndexByName("plansapps")->save();
        }
        $table = $this->table("apps_plans_settings");
        $table->addIndex(['apps_plans_id'], ['name' => "plansapps", 'unique' => false])->save();
        
        $table = $this->table("company_branches", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_mysql500_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])
            ->addColumn('company_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'id'])
            ->addColumn('users_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_id'])
            ->addColumn('name', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'users_id'])
            ->addColumn('description', 'string', ['null' => true, 'limit' => 255, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'name'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'description'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'boolean', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])
            ->save();

        $table = $this->table("currency", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_mysql500_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])
            ->addColumn('country', 'string', ['null' => true, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'id'])
            ->addColumn('currency', 'string', ['null' => true, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'country'])
            ->addColumn('code', 'string', ['null' => true, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'currency'])
            ->addColumn('symbol', 'string', ['null' => true, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'code'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'symbol'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'boolean', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])
            ->save();

        $this->table("access_list")->changeColumn('roles_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4"])->update();
        $this->table("access_list")->changeColumn('resources_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'roles_name'])->update();
        $this->table("access_list")->changeColumn('access_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'resources_name'])->update();
        $this->table("resources")->changeColumn('name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'id'])->update();
        $this->table("resources")->changeColumn('description', 'text', ['null' => true, 'limit' => 65535, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'name'])->update();
        $this->table("resources_accesses")->changeColumn('resources_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4"])->update();
        $this->table("resources_accesses")->changeColumn('access_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'resources_name'])->update();
        $this->table("roles")->changeColumn('name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'id'])->update();
        $this->table("roles")->changeColumn('description', 'text', ['null' => true, 'limit' => 65535, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'name'])->update();
        $this->table("roles_inherits")->changeColumn('roles_name', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4"])->update();
        $this->table("roles_inherits")->changeColumn('roles_inherit', 'string', ['null' => false, 'limit' => 32, 'collation' => "utf8mb4_unicode_520_ci", 'encoding' => "utf8mb4", 'after' => 'roles_name'])->update();
        
        $table = $this->table("apps", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("apps_roles", ['id' => false, 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("companies", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "					", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("company_settings", ['id' => false, 'primary_key' => ["name"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("languages", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->save();
        $table = $this->table("session_keys", ['id' => false, 'primary_key' => ["sessions_id", "users_id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("sessions", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("sources", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("suscriptions", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("user_company_apps", ['id' => false, 'primary_key' => ["company_id", "apps_id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("user_config", ['id' => false, 'primary_key' => ["users_id", "name"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("user_linked_sources", ['id' => false, 'primary_key' => ["users_id", "source_id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("users", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("users_associated_company", ['id' => false, 'primary_key' => ["users_id", "company_id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_ci", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("banlist", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_bin", 'comment' => "", 'row_format' => "Compact"]);
        $table->save();
        $table = $this->table("user_roles", ['id' => false, 'primary_key' => ["users_id", "apps_id", "company_id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_mysql500_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->save();
        $table = $this->table("access_list", ['id' => false, 'primary_key' => ["roles_name", "resources_name", "access_name", "apps_id"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->save();
        $table = $this->table("resources", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->save();
        $table = $this->table("resources_accesses", ['id' => false, 'primary_key' => ["resources_name", "access_name", "created_at"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->save();
        $table = $this->table("roles", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->save();
        $table = $this->table("roles_inherits", ['id' => false, 'primary_key' => ["roles_name", "roles_inherit"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_520_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->save();

        $this->execute("INSERT INTO `currency` VALUES (1, 'Albania', 'Leke', 'ALL', 'Lek', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (2, 'America', 'Dollars', 'USD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (3, 'Afghanistan', 'Afghanis', 'AFN', '؋', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (4, 'Argentina', 'Pesos', 'ARS', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (5, 'Aruba', 'Guilders', 'AWG', 'ƒ', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (6, 'Australia', 'Dollars', 'AUD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (7, 'Azerbaijan', 'New Manats', 'AZN', 'ман', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (8, 'Bahamas', 'Dollars', 'BSD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (9, 'Barbados', 'Dollars', 'BBD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (10, 'Belarus', 'Rubles', 'BYR', 'p.', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (11, 'Belgium', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (12, 'Beliz', 'Dollars', 'BZD', 'BZ$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (13, 'Bermuda', 'Dollars', 'BMD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (14, 'Bolivia', 'Bolivianos', 'BOB', '$b', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (15, 'Bosnia and Herzegovina', 'Convertible Marka', 'BAM', 'KM', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (16, 'Botswana', 'Pula', 'BWP', 'P', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (17, 'Bulgaria', 'Leva', 'BGN', 'лв', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (18, 'Brazil', 'Reais', 'BRL', 'R$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (19, 'Britain (United Kingdom)', 'Pounds', 'GBP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (20, 'Brunei Darussalam', 'Dollars', 'BND', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (21, 'Cambodia', 'Riels', 'KHR', '៛', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (22, 'Canada', 'Dollars', 'CAD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (23, 'Cayman Islands', 'Dollars', 'KYD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (24, 'Chile', 'Pesos', 'CLP', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (25, 'China', 'Yuan Renminbi', 'CNY', '¥', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (26, 'Colombia', 'Pesos', 'COP', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (27, 'Costa Rica', 'Colón', 'CRC', '₡', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (28, 'Croatia', 'Kuna', 'HRK', 'kn', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (29, 'Cuba', 'Pesos', 'CUP', '₱', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (30, 'Cyprus', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (31, 'Czech Republic', 'Koruny', 'CZK', 'Kč', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (32, 'Denmark', 'Kroner', 'DKK', 'kr', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (33, 'Dominican Republic', 'Pesos', 'DOP ', 'RD$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (34, 'East Caribbean', 'Dollars', 'XCD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (35, 'Egypt', 'Pounds', 'EGP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (36, 'El Salvador', 'Colones', 'SVC', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (37, 'England (United Kingdom)', 'Pounds', 'GBP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (38, 'Euro', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (39, 'Falkland Islands', 'Pounds', 'FKP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (40, 'Fiji', 'Dollars', 'FJD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (41, 'France', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (42, 'Ghana', 'Cedis', 'GHC', '¢', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (43, 'Gibraltar', 'Pounds', 'GIP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (44, 'Greece', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (45, 'Guatemala', 'Quetzales', 'GTQ', 'Q', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (46, 'Guernsey', 'Pounds', 'GGP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (47, 'Guyana', 'Dollars', 'GYD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (48, 'Holland (Netherlands)', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (49, 'Honduras', 'Lempiras', 'HNL', 'L', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (50, 'Hong Kong', 'Dollars', 'HKD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (51, 'Hungary', 'Forint', 'HUF', 'Ft', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (52, 'Iceland', 'Kronur', 'ISK', 'kr', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (53, 'India', 'Rupees', 'INR', 'Rp', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (54, 'Indonesia', 'Rupiahs', 'IDR', 'Rp', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (55, 'Iran', 'Rials', 'IRR', '﷼', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (56, 'Ireland', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (57, 'Isle of Man', 'Pounds', 'IMP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (58, 'Israel', 'New Shekels', 'ILS', '₪', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (59, 'Italy', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (60, 'Jamaica', 'Dollars', 'JMD', 'J$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (61, 'Japan', 'Yen', 'JPY', '¥', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (62, 'Jersey', 'Pounds', 'JEP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (63, 'Kazakhstan', 'Tenge', 'KZT', 'лв', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (64, 'Korea (North)', 'Won', 'KPW', '₩', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (65, 'Korea (South)', 'Won', 'KRW', '₩', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (66, 'Kyrgyzstan', 'Soms', 'KGS', 'лв', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (67, 'Laos', 'Kips', 'LAK', '₭', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (68, 'Latvia', 'Lati', 'LVL', 'Ls', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (69, 'Lebanon', 'Pounds', 'LBP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (70, 'Liberia', 'Dollars', 'LRD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (71, 'Liechtenstein', 'Switzerland Francs', 'CHF', 'CHF', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (72, 'Lithuania', 'Litai', 'LTL', 'Lt', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (73, 'Luxembourg', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (74, 'Macedonia', 'Denars', 'MKD', 'ден', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (75, 'Malaysia', 'Ringgits', 'MYR', 'RM', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (76, 'Malta', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (77, 'Mauritius', 'Rupees', 'MUR', '₨', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (78, 'Mexico', 'Pesos', 'MXN', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (79, 'Mongolia', 'Tugriks', 'MNT', '₮', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (80, 'Mozambique', 'Meticais', 'MZN', 'MT', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (81, 'Namibia', 'Dollars', 'NAD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (82, 'Nepal', 'Rupees', 'NPR', '₨', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (83, 'Netherlands Antilles', 'Guilders', 'ANG', 'ƒ', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (84, 'Netherlands', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (85, 'New Zealand', 'Dollars', 'NZD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (86, 'Nicaragua', 'Cordobas', 'NIO', 'C$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (87, 'Nigeria', 'Nairas', 'NGN', '₦', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (88, 'North Korea', 'Won', 'KPW', '₩', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (89, 'Norway', 'Krone', 'NOK', 'kr', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (90, 'Oman', 'Rials', 'OMR', '﷼', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (91, 'Pakistan', 'Rupees', 'PKR', '₨', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (92, 'Panama', 'Balboa', 'PAB', 'B/.', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (93, 'Paraguay', 'Guarani', 'PYG', 'Gs', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (94, 'Peru', 'Nuevos Soles', 'PEN', 'S/.', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (95, 'Philippines', 'Pesos', 'PHP', 'Php', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (96, 'Poland', 'Zlotych', 'PLN', 'zł', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (97, 'Qatar', 'Rials', 'QAR', '﷼', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (98, 'Romania', 'New Lei', 'RON', 'lei', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (99, 'Russia', 'Rubles', 'RUB', 'руб', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (100, 'Saint Helena', 'Pounds', 'SHP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (101, 'Saudi Arabia', 'Riyals', 'SAR', '﷼', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (102, 'Serbia', 'Dinars', 'RSD', 'Дин.', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (103, 'Seychelles', 'Rupees', 'SCR', '₨', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (104, 'Singapore', 'Dollars', 'SGD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (105, 'Slovenia', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (106, 'Solomon Islands', 'Dollars', 'SBD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (107, 'Somalia', 'Shillings', 'SOS', 'S', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (108, 'South Africa', 'Rand', 'ZAR', 'R', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (109, 'South Korea', 'Won', 'KRW', '₩', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (110, 'Spain', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (111, 'Sri Lanka', 'Rupees', 'LKR', '₨', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (112, 'Sweden', 'Kronor', 'SEK', 'kr', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (113, 'Switzerland', 'Francs', 'CHF', 'CHF', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (114, 'Suriname', 'Dollars', 'SRD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (115, 'Syria', 'Pounds', 'SYP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (116, 'Taiwan', 'New Dollars', 'TWD', 'NT$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (117, 'Thailand', 'Baht', 'THB', '฿', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (118, 'Trinidad and Tobago', 'Dollars', 'TTD', 'TT$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (119, 'Turkey', 'Lira', 'TRY', 'TL', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (120, 'Turkey', 'Liras', 'TRL', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (121, 'Tuvalu', 'Dollars', 'TVD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (122, 'Ukraine', 'Hryvnia', 'UAH', '₴', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (123, 'United Kingdom', 'Pounds', 'GBP', '£', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (124, 'United States of America', 'Dollars', 'USD', '$', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (125, 'Uruguay', 'Pesos', 'UYU', '$U', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (126, 'Uzbekistan', 'Sums', 'UZS', 'лв', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (127, 'Vatican City', 'Euro', 'EUR', '€', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (128, 'Venezuela', 'Bolivares Fuertes', 'VEF', 'Bs', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (129, 'Vietnam', 'Dong', 'VND', '₫', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (130, 'Yemen', 'Rials', 'YER', '﷼', '2018-12-05 01:00:00', NULL, 0);
                        INSERT INTO `currency` VALUES (131, 'Zimbabwe', 'Zimbabwe Dollars', 'ZWD', 'Z$', '2018-12-05 01:00:00', NULL, 0);");
    }
}

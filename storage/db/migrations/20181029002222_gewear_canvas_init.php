<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class GewearCanvasInit extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER DATABASE CHARACTER SET 'utf8mb4';");
        $this->execute("ALTER DATABASE COLLATE='utf8mb4_unicode_ci';");

        $table = $this->table('apps', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])
            ->addColumn('name', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'id'])
            ->addColumn('description', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'name'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'after' => 'description'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])
            ->save();

        //add default languages
        $data = [
            [
                'name' => 'Default',
                'description' => 'Gewaer Ecosystem',
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ], [
                'name' => 'CRM',
                'description' => 'CRM App',
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ]
        ];

        $table = $this->table('apps');
        $table->insert($data)->save();

        $this->execute("update apps set id = 0 where id = 1");
        $this->execute("update apps set id = 1 where id = 2");

        $table = $this->table('apps_roles', ['id' => false, 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10])
            ->addColumn('roles_name', 'string', ['null' => false, 'limit' => 32, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'apps_id'])
            ->save();

        $table = $this->table('companies', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '					', 'row_format' => 'Compact']);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])
            ->addColumn('name', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'id'])
            ->addColumn('profile_image', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'name'])
            ->addColumn('website', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'profile_image'])
            ->addColumn('users_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'website'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'after' => 'users_id'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])
            ->save();

        $table = $this->table('companies');

        if ($table->hasIndex('users_id')) {
            $table->removeIndexByName('users_id')->save();
        }
        $table = $this->table('companies');
        $table->addIndex(['users_id'], ['name' => 'users_id', 'unique' => false])->save();
        $table = $this->table('company_settings', ['id' => false, 'primary_key' => ['company_id', 'name'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'comment' => 'tabla donde se guardan las configuraciones en key value de los diferentes modelos

- general, zoho key, mandrill email settings
- modulo leads, agent default, rotation default , etc'])
            ->addColumn('name', 'string', ['null' => false, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'company_id'])
            ->addColumn('value', 'text', ['null' => false, 'limit' => 65535, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'name'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'after' => 'value'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])
            ->save();

         //add default companies
        $data = [
            [
                'name' => 'Canvas',
                'users_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ],
        ];

        $table = $this->table('companies');
        $table->insert($data)->save();


        $table = $this->table('company_settings');
        if ($table->hasIndex('index4')) {
            $table->removeIndexByName('index4')->save();
        }
        $table = $this->table('company_settings');
        $table->addIndex(['name'], ['name' => 'index4', 'unique' => false])->save();
        $table = $this->table('company_settings');
        if ($table->hasIndex('index5')) {
            $table->removeIndexByName('index5')->save();
        }
        $table = $this->table('company_settings');
        $table->addIndex(['company_id', 'name'], ['name' => 'index5', 'unique' => false])->save();

        $table = $this->table('session_keys', ['id' => false, 'primary_key' => ['sessions_id', 'users_id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('sessions_id', 'string', ['null' => false, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4'])
            ->addColumn('users_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 20, 'signed' => false, 'after' => 'sessions_id'])
            ->addColumn('last_ip', 'string', ['null' => true, 'limit' => 39, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'users_id'])
            ->addColumn('last_login', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 19, 'after' => 'last_ip'])
            ->save();

        $table = $this->table('session_keys');
        if ($table->hasIndex('last_login')) {
            $table->removeIndexByName('last_login')->save();
        }
        $table = $this->table('session_keys');
        $table->addIndex(['last_login'], ['name' => 'last_login', 'unique' => false])->save();
        $table = $this->table('session_keys');
        if ($table->hasIndex('user_id')) {
            $table->removeIndexByName('user_id')->save();
        }
        $table = $this->table('session_keys');
        $table->addIndex(['users_id'], ['name' => 'user_id', 'unique' => false])->save();
        $table = $this->table('session_keys');
        if ($table->hasIndex('session_id')) {
            $table->removeIndexByName('session_id')->save();
        }

        $table = $this->table('session_keys');
        $table->addIndex(['sessions_id'], ['name' => 'session_id', 'unique' => false])->save();
        $table = $this->table('sessions', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('id', 'string', ['null' => false, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4'])
            ->addColumn('users_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 20, 'signed' => false, 'after' => 'id'])
            ->addColumn('token', 'text', ['null' => false, 'limit' => 65535, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'users_id'])
            ->addColumn('start', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 19, 'after' => 'token'])
            ->addColumn('time', 'integer', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_BIG, 'precision' => 19, 'after' => 'start'])
            ->addColumn('ip', 'string', ['null' => false, 'limit' => 39, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'time'])
            ->addColumn('page', 'string', ['null' => false, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'ip'])
            ->addColumn('logged_in', 'enum', ['null' => false, 'default' => '0', 'limit' => 1, 'values' => ['0', '1'], 'after' => 'page'])
            ->addColumn('is_admin', 'enum', ['null' => true, 'default' => '0', 'limit' => 1, 'values' => ['0', '1'], 'after' => 'logged_in'])
            ->save();

        $table = $this->table('sessions');
        if ($table->hasIndex('user_id')) {
            $table->removeIndexByName('user_id')->save();
        }
        $table = $this->table('sessions');
        $table->addIndex(['users_id'], ['name' => 'user_id', 'unique' => false])->save();
        $table = $this->table('sessions');
        if ($table->hasIndex('time')) {
            $table->removeIndexByName('time')->save();
        }
        $table = $this->table('sessions');
        $table->addIndex(['time'], ['name' => 'time', 'unique' => false])->save();
        $table = $this->table('sessions');
        if ($table->hasIndex('logged_in')) {
            $table->removeIndexByName('logged_in')->save();
        }
        $table = $this->table('sessions');
        $table->addIndex(['logged_in'], ['name' => 'logged_in', 'unique' => false])->save();
        $table = $this->table('sessions');
        if ($table->hasIndex('start')) {
            $table->removeIndexByName('start')->save();
        }
        $table = $this->table('sessions');
        $table->addIndex(['start'], ['name' => 'start', 'unique' => false])->save();
        $table = $this->table('sources', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_SMALL, 'precision' => 5, 'signed' => false, 'identity' => 'enable'])
            ->addColumn('title', 'string', ['null' => false, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'id'])
            ->addColumn('url', 'string', ['null' => false, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'title'])
            ->addColumn('language_id', 'string', ['null' => true, 'limit' => 5, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'url'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'language_id'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])
            ->save();

        $table = $this->table('sources');
        if ($table->hasIndex('unq1')) {
            $table->removeIndexByName('unq1')->save();
        }
        $table = $this->table('sources');
        $table->addIndex(['url'], ['name' => 'unq1', 'unique' => true])->save();

        //add source
        $data = [
            [
                'title' => 'baka',
                'url' => 'baka.io',
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ], [
                'title' => 'androipapp',
                'url' => 'bakaapp.io',
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ], [
                'title' => 'iosapp',
                'url' => 'bakaios.io',
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ], [
                'title' => 'google',
                'url' => 'google.com',
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ],
        ];

        $table = $this->table('sources');
        $table->insert($data)->save();

        $table = $this->table('user_company_apps', ['id' => false, 'primary_key' => ['company_id', 'apps_id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'comment' => 'las apps que tiene contraída o usando el usuario

- leads
- agents
- office
- etc'])
            ->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_id'])
            ->save();

        $table = $this->table('user_config', ['id' => false, 'primary_key' => ['users_id', 'name'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('users_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 20, 'signed' => false])
            ->addColumn('name', 'string', ['null' => false, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'users_id'])
            ->addColumn('value', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'name'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'value'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])
            ->save();

        $table = $this->table('user_linked_sources', ['id' => false, 'primary_key' => ['users_id', 'source_id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('users_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 20, 'signed' => false])
            ->addColumn('source_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_SMALL, 'precision' => 5, 'signed' => false, 'after' => 'users_id'])
            ->addColumn('source_users_id', 'string', ['null' => false, 'limit' => 30, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'source_id'])
            ->addColumn('source_users_id_text', 'string', ['null' => true, 'limit' => 255, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'source_users_id'])
            ->addColumn('source_username', 'string', ['null' => false, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'source_users_id_text'])
            ->save();

        $table = $this->table('user_linked_sources');
        if ($table->hasIndex('user_id')) {
            $table->removeIndexByName('user_id')->save();
        }
        $table = $this->table('user_linked_sources');
        $table->addIndex(['users_id'], ['name' => 'user_id', 'unique' => false])->save();
        $table = $this->table('user_linked_sources');
        if ($table->hasIndex('source_user_id')) {
            $table->removeIndexByName('source_user_id')->save();
        }
        $table = $this->table('user_linked_sources');
        $table->addIndex(['source_users_id'], ['name' => 'source_user_id', 'unique' => false])->save();
        $table = $this->table('user_linked_sources');
        if ($table->hasIndex('source_user_id_text')) {
            $table->removeIndexByName('source_user_id_text')->save();
        }

        $table = $this->table('user_linked_sources');
        $table->addIndex(['source_username'], ['name' => 'source_username', 'unique' => false])->save();
        $table = $this->table('user_linked_sources');
        if ($table->hasIndex('user_id_2')) {
            $table->removeIndexByName('user_id_2')->save();
        }

        $table = $this->table('users', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_BIG, 'precision' => 19, 'identity' => 'enable'])
            ->addColumn('email', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'id'])
            ->addColumn('password', 'string', ['null' => true, 'limit' => 255, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'email'])
            ->addColumn('firstname', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'password'])
            ->addColumn('lastname', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'firstname'])
            ->addColumn('user_role', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'lastname'])
            ->addColumn('default_company', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'user_role'])
            ->addColumn('displayname', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'default_company'])
            ->addColumn('registered', 'datetime', ['null' => true, 'after' => 'displayname'])
            ->addColumn('lastvisit', 'datetime', ['null' => true, 'after' => 'registered'])
            ->addColumn('sex', 'char', ['null' => true, 'limit' => 1, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'lastvisit'])
            ->addColumn('timezone', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'sex'])
            ->addColumn('city_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'timezone'])
            ->addColumn('state_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'city_id'])
            ->addColumn('country_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'state_id'])
            ->addColumn('profile_privacy', 'char', ['null' => true, 'limit' => 1, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'country_id'])
            ->addColumn('profile_image', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'profile_privacy'])
            ->addColumn('profile_header', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'profile_image'])
            ->addColumn('profile_header_mobile', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'profile_header'])
            ->addColumn('user_active', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'profile_header_mobile'])
            ->addColumn('user_login_tries', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'user_active'])
            ->addColumn('user_last_loging_try', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'user_login_tries'])
            ->addColumn('session_time', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'user_last_loging_try'])
            ->addColumn('session_page', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'session_time'])
            ->addColumn('welcome', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'session_page'])
            ->addColumn('user_activation_key', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'welcome'])
            ->addColumn('user_activation_email', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'user_activation_key'])
            ->addColumn('user_activation_forgot', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'user_activation_email'])
            ->addColumn('language', 'string', ['null' => true, 'limit' => 5, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'user_activation_forgot'])
            ->addColumn('banned', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'language'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'banned'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'after' => 'updated_at'])
            ->addColumn('status', 'integer', ['null' => false, 'default' => '1', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'created_at'])
            ->addColumn('is_deleted', 'boolean', ['null' => true, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'status'])
            ->save();


        //add default languages
        $data = [
            [
                'email' => 'test@baka.io',
                'password' => password_hash('bakatest123567', PASSWORD_DEFAULT),
                'firstname' => 'Baka',
                'lastname' => 'Idiot',
                'default_company' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1,
                'is_deleted' => 0
            ], 
        ];

        $table = $this->table('users');
        $table->insert($data)->save();


        $table = $this->table('users_associated_company', ['id' => false, 'primary_key' => ['users_id', 'company_id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('users_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10])
            ->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'users_id'])
            ->addColumn('identify_id', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'company_id'])
            ->addColumn('user_active', 'boolean', ['null' => false, 'default' => '1', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'identify_id'])
            ->addColumn('user_role', 'string', ['null' => true, 'limit' => 45, 'collation' => 'utf8mb4_unicode_ci', 'encoding' => 'utf8mb4', 'after' => 'user_active'])
            ->save();

        $table = $this->table('users_associated_company');
        if ($table->hasIndex('users_id')) {
            $table->removeIndexByName('users_id')->save();
        }
        $table = $this->table('users_associated_company');
        $table->addIndex(['users_id', 'company_id'], ['name' => 'users_id', 'unique' => true])->save();
        $table = $this->table('users_associated_company');
        if ($table->hasIndex('users_id_2')) {
            $table->removeIndexByName('users_id_2')->save();
        }
        $table = $this->table('users_associated_company');
        $table->addIndex(['users_id', 'company_id'], ['name' => 'users_id_2', 'unique' => false])->save();
        $table = $this->table('banlist', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8_bin', 'comment' => '', 'row_format' => 'Compact']);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_MEDIUM, 'precision' => 7, 'signed' => false, 'identity' => 'enable'])
            ->addColumn('users_id', 'integer', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_BIG, 'precision' => 19, 'after' => 'id'])
            ->addColumn('ip', 'string', ['null' => false, 'default' => '', 'limit' => 35, 'collation' => 'latin1_swedish_ci', 'encoding' => 'latin1', 'after' => 'users_id'])
            ->addColumn('email', 'string', ['null' => true, 'limit' => 255, 'collation' => 'latin1_swedish_ci', 'encoding' => 'latin1', 'after' => 'ip'])
            ->save();

        $table = $this->table('banlist');
        if ($table->hasIndex('ban_ip_user_id')) {
            $table->removeIndexByName('ban_ip_user_id')->save();
        }
        $table = $this->table('banlist');
        $table->addIndex(['ip', 'users_id'], ['name' => 'ban_ip_user_id', 'unique' => false])->save();
    }
}

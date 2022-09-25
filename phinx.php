<?php

use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\envValue;

// This file will end up in the root of the project
require_once './library/Core/autoload.php';

return [
    'paths'         => [
        'migrations' => appPath('/storage/db/migrations'),
        'seeds'      => appPath('/storage/db/seeds'),
    ],
    'environments'  => [
        'default_migration_table' => 'ut_migrations',
        'default_database'        => 'development',
        'production'              => [
            'adapter' => 'mysql',
            'host'    => 'localhost',
            'name'    => 'tdm_prod',
            'user'    => 'root',
            'pass'    => 'password',
            'port'    => 3306,
            'charset' => 'utf8',
        ],
        'development'             => [
            'adapter' => envValue('DATA_API_MYSQL_ADAPTER', 'mysql'),
            'host'    => envValue('DATA_API_MYSQL_HOST', '127.0.0.1'),
            'name'    => envValue('DATA_API_MYSQL_NAME', 'phalcon_api'),
            'user'    => envValue('DATA_API_MYSQL_USER', 'root'),
            'pass'    => envValue('DATA_API_MYSQL_PASS', 'password'),
            'port'    => envValue('DATA_API_MYSQL_PORT', 3306),
            'charset' => envValue('DATA_TDM_MYSQL_CHARSET', 'utf8'),
        ],
        'testing'                 => [
            'adapter' => envValue('DATA_API_MYSQL_ADAPTER', 'mysql'),
            'host'    => envValue('DATA_API_MYSQL_HOST', '127.0.0.1'),
            'name'    => envValue('DATA_API_MYSQL_NAME', 'phalcon_api'),
            'user'    => envValue('DATA_API_MYSQL_USER', 'root'),
            'pass'    => envValue('DATA_API_MYSQL_PASS', 'password'),
            'port'    => envValue('DATA_API_MYSQL_PORT', 3306),
            'charset' => envValue('DATA_TDM_MYSQL_CHARSET', 'utf8'),
        ],
    ],
    'version_order' => 'creation',
];

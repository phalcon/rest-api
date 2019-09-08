<?php

return [
    'paths'         => [
        'migrations' => getenv('PHINX_CONFIG_DIR') . 'storage/db/migrations',
        'seeds'      => getenv('PHINX_CONFIG_DIR') . 'storage/db/seeds',
    ],
    'environments'  => [
        'default_migration_table' => 'ut_migrations',
        'default_database'        => 'development',
        'production'              => [
            'adapter' => 'mysql',
            'host'    => getenv('DATA_API_MYSQL_HOST'),
            'name'    => getenv('DATA_API_MYSQL_NAME'),
            'user'    => getenv('DATA_API_MYSQL_USER'),
            'pass'    => getenv('DATA_API_MYSQL_PASS'),
            'port'    => 3306,
            'charset' => 'utf8',
        ],
        'development'             => [
            'adapter' => 'mysql',
            'host'    => getenv('DATA_API_MYSQL_HOST'),
            'name'    => getenv('DATA_API_MYSQL_NAME'),
            'user'    => getenv('DATA_API_MYSQL_USER'),
            'pass'    => getenv('DATA_API_MYSQL_PASS'),
            'port'    => 3306,
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation',
];

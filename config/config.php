<?php

use function Niden\Functions\appPath;
use function Niden\Functions\envValue;

return [
    'app'        => [
        'version'      => envValue('VERSION', time()),
        'timezone'     => envValue('APP_TIMEZONE', 'UTC'),
        'debug'        => envValue('APP_DEBUG', false),
        'env'          => envValue('APP_ENV', 'development'),
        'devMode'      => boolval(
            'development' === envValue('APP_ENV', 'development')
        ),
        'baseUri'      => envValue('APP_BASE_URI'),
        'supportEmail' => envValue('APP_SUPPORT_EMAIL'),
        'time'         => microtime(true),
    ],
    'db'         => [
        'host'     => envValue('DATA_API_MYSQL_HOST'),
        'dbname'   => envValue('DATA_API_MYSQL_NAME'),
        'username' => envValue('DATA_API_MYSQL_USER'),
        'password' => envValue('DATA_API_MYSQL_PASS'),
        'encoding' => 'utf8',
    ],
    'cache'      => [
        'data'     => [
            'front' => [
                'adapter' => 'Data',
                'options' => [
                    'lifetime' => envValue('CACHE_LIFETIME'),
                ],
            ],
            'back'  => [
                'dev'  => [
                    'adapter' => 'File',
                    'options' => [
                        'cacheDir' => appPath('storage/cache/data/'),
                    ],
                ],
                'prod' => [
                    'adapter' => 'Libmemcached',
                    'options' => [
                        'servers' => [
                            [
                                'host'   => envValue('DATA_API_MEMCACHED_HOST'),
                                'port'   => envValue('DATA_API_MEMCACHED_PORT'),
                                'weight' => envValue('DATA_API_MEMCACHED_WEIGHT'),
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'metadata' => [
            'dev'  => [
                'adapter' => 'Memory',
                'options' => [],
            ],
            'prod' => [
                'adapter' => 'Files',
                'options' => [
                    'metaDataDir' => appPath('storage/cache/metadata/'),
                ],
            ],
        ],
    ],
    'logger'     => [
        'name'     => envValue('LOGGER_DEFAULT_FILENAME', 'api.log'),
        'path'     => envValue('LOGGER_DEFAULT_PATH', 'storage/logs'),
    ],
];

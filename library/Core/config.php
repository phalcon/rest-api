<?php

use function Gewaer\Core\appPath;
use function Gewaer\Core\envValue;

return [
    'application' => [ //@todo migration to app
        'production ' => getenv('PRODUCTION'),
        'development ' => getenv('DEVELOPMENT'),
        'jwtSecurity' => getenv('JWT_SECURITY'),
        'debug' => [
            'profile' => getenv('DEBUG_PROFILE'),
            'logQueries' => getenv('DEBUG_QUERY'),
            'logRequest' => getenv('DEBUG_REQUEST')
        ],
    ],
    'app' => [
        //GEWAER is a multi entity app encosystem so we need what is the current api ID for this api
        'id' => envValue('GEWAER_APP_ID', 1),
        'frontEndUrl' => envValue('FRONTEND_URL'),
        'version' => envValue('VERSION', time()),
        'timezone' => envValue('APP_TIMEZONE', 'UTC'),
        'debug' => envValue('APP_DEBUG', false),
        'env' => envValue('APP_ENV', 'development'),
        'production ' => envValue('PRODUCTION', 0) == 1 ? 1 : 0,
        'logsReport' => envValue('APP_LOGS_REPORT', false),
        'devMode' => boolval(
            'development' === envValue('APP_ENV', 'development')
        ),
        'baseUri' => envValue('APP_BASE_URI'),
        'supportEmail' => envValue('APP_SUPPORT_EMAIL'),
        'time' => microtime(true),
        'namespaceName' => envValue('APP_NAMESPACE'),
    ],
    'cache' => [
        'data' => [
            'front' => [
                'adapter' => 'Data',
                'options' => [
                    'lifetime' => envValue('CACHE_LIFETIME'),
                ],
            ],
            'back' => [
                'dev' => [
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
                                'host' => envValue('DATA_API_MEMCACHED_HOST'),
                                'port' => envValue('DATA_API_MEMCACHED_PORT'),
                                'weight' => envValue('DATA_API_MEMCACHED_WEIGHT'),
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'metadata' => [
            'dev' => [
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
    'email' => [
        'driver' => 'smtp',
        'host' => envValue('EMAIL_HOST'),
        'port' => envValue('EMAIL_PORT'),
        'username' => envValue('EMAIL_USER'),
        'password' => envValue('EMAIL_PASS'),
        'from' => [
            'email' => envValue('EMAIL_FROM_PRODUCTION'),
            'name' => envValue('EMAIL_FROM_NAME_PRODUCTION'),
        ],
        'debug' => [
            'from' => [
                'email' => envValue('EMAIL_FROM_DEBUG'),
                'name' => envValue('EMAIL_FROM_NAME_DEBUG'),
            ],
        ],
    ],
    'beanstalk' => [
        'host' => getenv('BEANSTALK_HOST'),
        'port' => getenv('BEANSTALK_PORT'),
        'prefix' => getenv('BEANSTALK_PREFIX'),
    ],
    'jwt' => [
        'secretKey' => envValue('APP_JWT_TOKEN'),
        'payload' => [
            'exp' => envValue('APP_JWT_SESSION_EXPIRATION', 1440),
            'iss' => 'phalcon-jwt-auth',
        ],
    ],
    'pusher' => [
        'id' => envValue('PUSHER_ID'),
        'key' => envValue('PUSHER_KEY'),
        'secret' => envValue('PUSHER_SECRET'),
        'cluster' => envValue('PUSHER_SECRET'),
        'queue' => envValue('PUSHER_QUEUE'),
    ],
    'stripe' => [
        'secret' => getenv('STRIPE_SECRET'),
        'public' => getenv('STRIPE_PUBLIC')
    ]
];

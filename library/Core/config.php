<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\envValue;

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
    'cache'      => [
        'adapter' => 'libmemcached',
        'options' => [
            'libmemcached' => [
                'servers'  => [
                    0 => [
                        'host'   => envValue('DATA_API_MEMCACHED_HOST', '127.0.0.1'),
                        'port'   => envValue('DATA_API_MEMCACHED_PORT', 11211),
                        'weight' => envValue('DATA_API_MEMCACHED_WEIGHT', 100),
                    ],
                ],
                'client'   => [
                    \Memcached::OPT_PREFIX_KEY => 'api-',
                ],
                'lifetime' => envValue('CACHE_LIFETIME', 86400),
                'prefix'   => 'data-',
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
];

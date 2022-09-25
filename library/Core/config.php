<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use Phalcon\Mvc\Model\MetaData\Memory;
use Phalcon\Mvc\Model\MetaData\Redis;

use function Phalcon\Api\Core\envValue;

return [
    'app'      => [
        'version'      => envValue('VERSION', time()),
        'timezone'     => envValue('APP_TIMEZONE', 'UTC'),
        'debug'        => envValue('APP_DEBUG', false),
        'env'          => envValue('APP_ENV', 'development'),
        'devMode'      => 'development' === envValue('APP_ENV', 'development'),
        'baseUri'      => envValue('APP_BASE_URI'),
        'supportEmail' => envValue('APP_SUPPORT_EMAIL'),
        'time'         => hrtime(true),
    ],
    'cache'    => [
        'adapter' => 'redis',
        'options' => [
            'host'     => envValue('DATA_API_REDIS_HOST', '127.0.0.1'),
            'port'     => envValue('DATA_API_REDIS_PORT', 6379),
            'index'    => envValue('DATA_API_REDIS_WEIGHT', 0),
            'lifetime' => envValue('CACHE_LIFETIME', 86400),
            'prefix'   => 'data-',
        ],
    ],
    'metadata' => [
        'dev'  => [
            'adapter' => Memory::class,
            'options' => [],
        ],
        'prod' => [
            'adapter' => Redis::class,
            'options' => [
                'host'     => envValue('DATA_API_REDIS_HOST', '127.0.0.1'),
                'port'     => envValue('DATA_API_REDIS_PORT', 6379),
                'index'    => envValue('DATA_API_REDIS_WEIGHT', 0),
                'lifetime' => envValue('CACHE_LIFETIME', 86400),
                'prefix'   => 'metadata-',
            ],
        ],
    ],
];

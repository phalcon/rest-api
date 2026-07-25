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

/**
 * The cache and the production metadata store are the same Redis, differing
 * only in the prefix they namespace their keys with. Stated once: the last time
 * these two blocks were maintained separately, a `lifetime` that arrived as a
 * string had to be cast in both, and missing either one returns HTTP 500 from
 * every endpoint that reads a record.
 */
$redisOptions = static fn (string $prefix): array => [
    'host'     => envValue('DATA_API_REDIS_HOST', '127.0.0.1'),
    'port'     => envValue('DATA_API_REDIS_PORT', 6379),
    'index'    => envValue('DATA_API_REDIS_WEIGHT', 0),
    // Env values arrive as strings; AbstractAdapter::getTtl() returns int.
    'lifetime' => (int) envValue('CACHE_LIFETIME', 86400),
    'prefix'   => $prefix,
];

/**
 * Everything the application reads from the environment is read here. Above
 * this file, components ask the `config` service - they used to reach for
 * envValue() themselves, which meant this file described only some of the
 * configuration and nothing described the rest.
 */
return [
    'app'      => [
        'version'      => envValue('VERSION', time()),
        'timezone'     => envValue('APP_TIMEZONE', 'UTC'),
        'debug'        => envValue('APP_DEBUG', false),
        'env'          => envValue('APP_ENV', 'development'),
        'devMode'      => 'development' === envValue('APP_ENV', 'development'),
        'baseUri'      => envValue('APP_BASE_URI'),
        'url'          => envValue('APP_URL', 'http://localhost'),
        'supportEmail' => envValue('APP_SUPPORT_EMAIL'),
        'time'         => hrtime(true),
    ],
    'cache'    => [
        'adapter' => 'redis',
        'options' => $redisOptions('data-'),
    ],
    'database' => [
        'host'     => envValue('DATA_API_MYSQL_HOST', 'localhost'),
        'username' => envValue('DATA_API_MYSQL_USER', 'phalcon'),
        'password' => envValue('DATA_API_MYSQL_PASS', ''),
        'dbname'   => envValue('DATA_API_MYSQL_NAME', 'phalcon_api'),
    ],
    'logger'   => [
        'filename' => envValue('LOGGER_DEFAULT_FILENAME', 'api.log'),
        'path'     => envValue('LOGGER_DEFAULT_PATH', 'storage/logs'),
    ],
    'metadata' => [
        'dev'  => [
            'adapter' => Memory::class,
            'options' => [],
        ],
        'prod' => [
            'adapter' => Redis::class,
            'options' => $redisOptions('metadata-'),
        ],
    ],
    'token'    => [
        // Env values arrive as strings; these are used in arithmetic.
        'audience'   => envValue('TOKEN_AUDIENCE', 'https://phalcon.io'),
        'expiration' => (int) envValue('TOKEN_EXPIRATION', 86400),
        'notBefore'  => (int) envValue('TOKEN_NOT_BEFORE', 0),
    ],
];

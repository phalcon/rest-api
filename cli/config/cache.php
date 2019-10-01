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

use function Phalcon\Api\Core\envValue;

return [
    'adapter' => envValue('CACHE_ADAPTER'),
    'servers'  => [
        0 => [
            'host'   => envValue('DATA_API_MEMCACHED_HOST', '127.0.0.1'),
            'port'   => envValue('DATA_API_MEMCACHED_PORT', 11211),
            'weight' => envValue('DATA_API_MEMCACHED_WEIGHT', 100),
        ],
    ],
    'client'   => [
        //\Memcached::OPT_HASH       => \Memcached::HASH_MD5,
        \Memcached::OPT_PREFIX_KEY => 'api-',
    ],
    'lifetime' => envValue('CACHE_LIFETIME', 86400),
    'prefix'   => 'data-',
];

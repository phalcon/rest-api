<?php

use function Niden\Core\envValue;

return [
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

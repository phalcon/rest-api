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

/**
 * Talon suite map.
 *
 * Without this file Talon derives one suite per `phpunit*.xml*` file and names
 * it after the filename. All four suites live in a single PHPUnit config, so
 * they are mapped here instead and told apart with `--testsuite`. That keeps
 * `talon run api` working while the configuration stays in one place.
 *
 * `talon run all` runs every suite below, each as its own PHPUnit process.
 */
return [
    'default' => 'unit',
    'suites'  => [
        'unit'        => [
            'config' => 'resources/phpunit.xml.dist',
            'args'   => ['--testsuite', 'unit'],
        ],
        'integration' => [
            'config' => 'resources/phpunit.xml.dist',
            'args'   => ['--testsuite', 'integration'],
        ],
        'api'         => [
            'config' => 'resources/phpunit.xml.dist',
            'args'   => ['--testsuite', 'api'],
        ],
        'cli'         => [
            'config' => 'resources/phpunit.xml.dist',
            'args'   => ['--testsuite', 'cli'],
        ],
    ],
];

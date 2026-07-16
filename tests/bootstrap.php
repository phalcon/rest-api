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

use Dotenv\Dotenv;
use Phalcon\Talon\Settings;
use Phalcon\Talon\Talon;

define('API_TESTS', true);

/**
 * The suite runs under the CLI SAPI, where there is no request URI.
 */
$_SERVER['REQUEST_URI'] = '/';

/**
 * Registers the application namespaces, pulls in the Composer autoloader and
 * loads the root .env.
 */
require_once dirname(__FILE__, 2) . '/src/Core/autoload.php';

/**
 * Talon reads DATA_MYSQL_* / DATA_REDIS_*, while the application reads
 * DATA_API_*. Settings::fromEnv() checks getenv() before $_ENV, so real
 * container and CI variables win and this file only fills the gaps.
 */
Dotenv::createImmutable(dirname(__FILE__), '.env.test')->safeLoad();

Talon::boot(Settings::fromEnv());

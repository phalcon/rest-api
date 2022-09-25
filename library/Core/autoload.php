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
use Phalcon\Autoload\Loader;

use function Phalcon\Api\Core\appPath;

// Register the autoloader
require __DIR__ . '/functions.php';

$loader     = new Loader();
$namespaces = [
    'Phalcon\Api'                 => appPath('/library'),
    'Phalcon\Api\Api\Controllers' => appPath('/api/controllers'),
    'Phalcon\Api\Cli\Tasks'       => appPath('/cli/tasks'),
    'Phalcon\Api\Tests'           => appPath('/tests'),
];

$loader->setNamespaces($namespaces);
$loader->register();

/**
 * Composer Autoloader
 */
require appPath('/vendor/autoload.php');

// Load environment
(Dotenv::createImmutable(appPath()))->load();

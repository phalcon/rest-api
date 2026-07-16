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

// appPath() and friends; needed before anything can be located
require __DIR__ . '/functions.php';

/**
 * Composer first. On the v5 variant Phalcon is a C extension and its classes
 * exist unconditionally, but on v6 the framework IS a Composer package - so
 * `new Loader()` below cannot be reached without Composer's autoloader already
 * registered. Loading it second worked only because the extension happened to
 * be there.
 */
require appPath('/vendor/autoload.php');

/**
 * The loader resolves by namespace prefix, so a single entry covers the whole
 * source tree: Phalcon\Api\Models\Users -> src/Models/Users.php, and
 * Phalcon\Api\Api\Controllers\LoginController -> src/Api/Controllers/LoginController.php.
 */
$loader     = new Loader();
$namespaces = [
    'Phalcon\Api'       => appPath('/src'),
    'Phalcon\Api\Tests' => appPath('/tests'),
];

$loader->setNamespaces($namespaces);
$loader->register();

// Load environment
(Dotenv::createImmutable(appPath()))->load();

<?php

use Dotenv\Dotenv;
use Phalcon\Loader;
use function Niden\Core\appPath;

// Register the auto loader
require __DIR__ . '/functions.php';

$loader = new Loader();
$namespaces = [
    'Niden' => appPath('/library'),
    'Baka' => appPath('/library'),
    'Baka\Api\Controllers' => appPath('/api/controllers'),
    'Baka\Cli\Tasks' => appPath('/cli/tasks'),
    'Baka\Tests' => appPath('/tests'),
];

$loader->registerNamespaces($namespaces);

$loader->register();

/**
 * Composer Autoloader
 */
require appPath('/vendor/autoload.php');

// Load environment
(new Dotenv(appPath()))->overload();

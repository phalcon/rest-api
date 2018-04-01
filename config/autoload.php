<?php

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Phalcon\Loader;
use function Niden\Functions\appPath;

// Register the auto loader
require __DIR__ . '/functions.php';

$loader     = new Loader();
$namespaces = [
    'Niden'       => appPath('/library'),
    'Niden\Tests' => appPath('/tests'),
];

$loader->registerNamespaces($namespaces);

$loader->register();

/**
 * Composer Autoloader
 */
require appPath('/vendor/autoload.php');

// Load environment
try {
    (new Dotenv(appPath()))->load();
} catch (InvalidPathException $e) {
    // Skip
}

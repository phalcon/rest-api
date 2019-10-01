<?php

define('API_TESTS', true);

/**
 * As current request is inside CLI
 * There are no REQUEST URI
 */
$_SERVER['REQUEST_URI'] = '/';

require __DIR__ . '/../library/Core/autoload.php';

ini_set('date.timezone', 'UTC');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

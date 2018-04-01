<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;

require_once __DIR__ . '/../config/autoload.php';

$diContainer = new FactoryDefault();

$app = new Micro($diContainer);

$app->get(
    '/',
    function () {
        echo 'Phalcon API';
    }
);

$app->notFound(
    function () {
        echo 'Route not found';
    }
);

if (true === defined('API_TESTS')) {
    return $app;
} else {
    return $app->handle();
}

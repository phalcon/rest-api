<?php

use Phalcon\Mvc\Micro;

require __DIR__ . '/../config/autoload.php';

$app = new Micro();

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

$app->handle();

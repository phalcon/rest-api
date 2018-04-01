<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;

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

return $app;

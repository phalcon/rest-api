<?php

use Phalcon\Mvc\Micro;

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

return $app;

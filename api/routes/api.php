<?php

use Baka\Http\RouterCollection;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for api.
 */

$router = new RouterCollection($application);
$router->setPrefix('/v1');
$router->get('/', [
    'Baka\Api\Controllers\IndexController',
    'index',
]);

$router->get('/status', [
    'Baka\Api\Controllers\IndexController',
    'status',
]);

$router->mount();

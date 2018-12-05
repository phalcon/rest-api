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
    'Gewaer\Api\Controllers\IndexController',
    'index',
    'options' => [
        'jwt' => false,
    ]
]);

$router->get('/status', [
    'Gewaer\Api\Controllers\IndexController',
    'status',
    'options' => [
        'jwt' => false,
    ]
]);

$router->get('/timezones', [
    'Gewaer\Api\Controllers\TimeZonesController',
    'index',
]);

/**
 * Authentification Calls
 * @var [type]
 */
$router->post('/auth', [
    'Gewaer\Api\Controllers\AuthController',
    'login',
    'options' => [
        'jwt' => false,
    ]
]);

//asociate mobile devices
$router->post('/users/{id}/devices', [
    'Gewaer\Api\Controllers\UsersController',
    'devices',
]);

/**
 * Need to understand if using this can be a performance disadvantage in the future
 */
$defaultCrudRoutes = [
    'users',
    'companies',
    'languages',
    'AppsPlans' => 'apps-plans',
    'roles',
];

foreach ($defaultCrudRoutes as $key => $route) {
    //set the controller name
    $name = is_int($key) ? $route : $key;
    $controllerName = ucfirst($name) . 'Controller';

    $router->get('/' . $route, [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'index',
    ]);

    $router->post('/' . $route, [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'create',
    ]);

    $router->get('/' . $route . '/{id}', [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'getById',
    ]);

    $router->put('/' . $route . '/{id}', [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'edit',
    ]);

    $router->put('/' . $route, [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'multipleUpdates',
    ]);

    $router->delete('/' . $route . '/{id}', [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'delete',
    ]);
}

$router->post('/users', [
    'Gewaer\Api\Controllers\AuthController',
    'signup',
    'options' => [
        'jwt' => false,
    ]
]);

$router->put('/auth/logout', [
    'Gewaer\Api\Controllers\AuthController',
    'logout',
]);

$router->post('/auth/forgot', [
    'Gewaer\Api\Controllers\AuthController',
    'recover',
    'options' => [
        'jwt' => false,
    ]
]);

$router->post('/auth/reset/{key}', [
    'Gewaer\Api\Controllers\AuthController',
    'reset',
    'options' => [
        'jwt' => false,
    ]
]);

$router->mount();

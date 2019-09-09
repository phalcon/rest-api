<?php

/**
 * Enabled providers. Order does matter
 */

use Phalcon\Api\Providers\CacheDataProvider;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\DatabaseProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Api\Providers\RequestProvider;
use Phalcon\Api\Providers\ResponseProvider;
use Phalcon\Api\Providers\RouterProvider;

return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
    RequestProvider::class,
    ResponseProvider::class,
    RouterProvider::class,
    CacheDataProvider::class,
];

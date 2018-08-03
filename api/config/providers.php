<?php

/**
 * Enabled providers. Order does matter
 */

use Niden\Providers\CacheDataProvider;
use Niden\Providers\ConfigProvider;
use Niden\Providers\DatabaseProvider;
use Niden\Providers\ErrorHandlerProvider;
use Niden\Providers\LoggerProvider;
use Niden\Providers\ModelsMetadataProvider;
use Niden\Providers\RequestProvider;
use Niden\Providers\ResponseProvider;
use Niden\Providers\RouterProvider;

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

<?php

/**
 * Enabled providers. Order does matter
 */

use Niden\Providers\CacheDataProvider;
use Niden\Providers\CliDispatcherProvider;
use Niden\Providers\ConfigProvider;
use Niden\Providers\DatabaseProvider;
use Niden\Providers\ErrorHandlerProvider;
use Niden\Providers\LoggerProvider;
use Niden\Providers\ModelsMetadataProvider;

return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
    CliDispatcherProvider::class,
    CacheDataProvider::class,
];

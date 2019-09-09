<?php

/**
 * Enabled providers. Order does matter
 */

use Phalcon\Api\Providers\CacheDataProvider;
use Phalcon\Api\Providers\CliDispatcherProvider;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\DatabaseProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;

return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
    CliDispatcherProvider::class,
    CacheDataProvider::class,
];

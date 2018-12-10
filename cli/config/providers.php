<?php

/**
 * Enabled providers. Order does matter
 */

use Gewaer\Providers\CacheDataProvider;
use Gewaer\Providers\CliDispatcherProvider;
use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\DatabaseProvider;
use Gewaer\Providers\ErrorHandlerProvider;
use Gewaer\Providers\LoggerProvider;
use Gewaer\Providers\ModelsMetadataProvider;
use Gewaer\Providers\QueueProvider;
use Gewaer\Providers\MailProvider;
use Gewaer\Providers\RedisProvider;
use Gewaer\Providers\PusherProvider;
use Gewaer\Providers\AclProvider;
use Gewaer\Providers\AppProvider;

return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
    CliDispatcherProvider::class,
    CacheDataProvider::class,
    QueueProvider::class,
    MailProvider::class,
    RedisProvider::class,
    PusherProvider::class,
    AclProvider::class,
    AppProvider::class,
];

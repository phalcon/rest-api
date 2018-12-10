<?php

/**
 * Enabled providers. Order does matter
 */

use Gewaer\Providers\CacheDataProvider;
use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\DatabaseProvider;
use Gewaer\Providers\ErrorHandlerProvider;
use Gewaer\Providers\LoggerProvider;
use Gewaer\Providers\ModelsMetadataProvider;
use Gewaer\Providers\RouterProvider;
use Gewaer\Providers\SessionProvider;
use Gewaer\Providers\QueueProvider;
use Gewaer\Providers\MailProvider;
use Gewaer\Providers\RedisProvider;
use Gewaer\Providers\RequestProvider;
use Gewaer\Providers\AclProvider;
use Gewaer\Providers\AppProvider;

return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
    RequestProvider::class,
    RouterProvider::class,
    CacheDataProvider::class,
    SessionProvider::class,
    QueueProvider::class,
    MailProvider::class,
    RedisProvider::class,
    RedisProvider::class,
    AclProvider::class,
    AppProvider::class,
];

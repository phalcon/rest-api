<?php

/**
 * Enabled providers. Order does matter
 */

use Baka\Providers\CacheDataProvider;
use Baka\Providers\ConfigProvider;
use Baka\Providers\DatabaseProvider;
use Baka\Providers\ErrorHandlerProvider;
use Baka\Providers\LoggerProvider;
use Baka\Providers\ModelsMetadataProvider;
use Baka\Providers\RouterProvider;
use Baka\Providers\SessionProvider;
use Baka\Providers\QueueProvider;
use Baka\Providers\MailProvider;
use Baka\Providers\RedisProvider;

return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
    RouterProvider::class,
    CacheDataProvider::class,
    SessionProvider::class,
    QueueProvider::class,
    MailProvider::class,
    RedisProvider::class,
];

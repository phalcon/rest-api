<?php

/**
 * Enabled providers. Order does matter
 */

use Niden\Providers\CliDispatcherProvider;
use Niden\Providers\ConfigProvider;
use Niden\Providers\DatabaseProvider;
use Niden\Providers\ErrorHandlerProvider;
use Niden\Providers\EventsManagerProvider;
use Niden\Providers\LoggerProvider;

return [
    ConfigProvider::class,
    EventsManagerProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    CliDispatcherProvider::class,
];

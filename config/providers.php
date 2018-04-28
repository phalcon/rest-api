<?php

/**
 * Enabled providers. Order does matter
 */
use Niden\Providers\ConfigProvider;
use Niden\Providers\ErrorHandlerProvider;
use Niden\Providers\EventsManagerProvider;
use Niden\Providers\LoggerProvider;
use Niden\Providers\ResponseProvider;
use Niden\Providers\RouterProvider;

return [
    ConfigProvider::class,
    EventsManagerProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    ResponseProvider::class,
    RouterProvider::class,
];

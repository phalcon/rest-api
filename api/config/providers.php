<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
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

/**
 * Enabled providers. Order does matter
 */
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

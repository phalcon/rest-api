<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\DatabaseProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;

/**
 * The providers every entry point needs, in the order they need them.
 *
 * Order does matter, and it is the reason this list is stated once: `config`
 * has to exist before the logger and the database can read theirs from it, and
 * the error handler wants the logger. Both entry points had the same five
 * lines in the same sequence, which is a sequence that could be got wrong in
 * one place and right in the other.
 *
 * `CacheDataProvider` is deliberately not here - the API and the CLI register
 * it at different points in their own lists.
 */
return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
];

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

use Phalcon\Api\Providers\CacheDataProvider;
use Phalcon\Api\Providers\QueryServiceProvider;
use Phalcon\Api\Providers\RequestProvider;
use Phalcon\Api\Providers\ResponseProvider;
use Phalcon\Api\Providers\RouterProvider;
use Phalcon\Api\Providers\TokenServiceProvider;
use Phalcon\Api\Providers\UsersRepositoryProvider;

use function Phalcon\Api\Core\appPath;

/**
 * Enabled providers. Order does matter.
 *
 * The shared prefix - config, logger, error handler, database, metadata - is in
 * src/Core/providers.php, which the CLI requires as well.
 */
return array_merge(
    require appPath('src/Core/providers.php'),
    [
        RequestProvider::class,
        ResponseProvider::class,
        RouterProvider::class,
        CacheDataProvider::class,
        QueryServiceProvider::class,
        UsersRepositoryProvider::class,
        TokenServiceProvider::class,
    ]
);

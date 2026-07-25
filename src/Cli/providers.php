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
use Phalcon\Api\Providers\CliDispatcherProvider;

use function Phalcon\Api\Core\appPath;

/**
 * Enabled providers. Order does matter.
 *
 * The shared prefix - config, logger, error handler, database, metadata - is in
 * src/Core/providers.php, which the API requires as well.
 */
return array_merge(
    require appPath('src/Core/providers.php'),
    [
        CliDispatcherProvider::class,
        CacheDataProvider::class,
    ]
);

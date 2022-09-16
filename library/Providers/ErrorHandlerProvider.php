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

namespace Phalcon\Api\Providers;

use Phalcon\Api\ErrorHandler;
use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Logger\Logger;

use function date_default_timezone_set;
use function error_reporting;
use function ini_set;
use function register_shutdown_function;
use function set_error_handler;

use const E_ALL;

class ErrorHandlerProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        /** @var Logger $logger */
        $logger = $container->getShared('logger');
        /** @var Config $registry */
        $config = $container->getShared('config');

        date_default_timezone_set($config->path('app.timezone'));
        ini_set('display_errors', 'Off');
        error_reporting(E_ALL);

        $handler = new ErrorHandler($logger, $config);
        set_error_handler([$handler, 'handle']);
        register_shutdown_function([$handler, 'shutdown']);
    }
}

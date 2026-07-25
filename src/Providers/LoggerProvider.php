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

use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Logger;

use function Phalcon\Api\Core\appPath;

class LoggerProvider implements ServiceProviderInterface
{
    /**
     * Registers the logger component
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        /** @var Config $config */
        $config = $container->getShared('config');

        $container->setShared(
            'logger',
            function () use ($config) {
                /** @var string $logName */
                $logName = $config->path('logger.filename', 'api.log');
                /** @var string $logPath */
                $logPath = $config->path('logger.path', 'storage/logs');
                $logFile = appPath($logPath) . '/' . $logName . '.log';
                $adapter = new Stream($logFile);
                $logger  = new Logger(
                    $logName,
                    [
                        'main' => $adapter,
                    ]
                );

                return $logger;
            }
        );
    }
}

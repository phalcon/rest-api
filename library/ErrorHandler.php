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

namespace Phalcon\Api;

use Phalcon\Config\Config;
use Phalcon\Logger\Exception;
use Phalcon\Logger\Logger;

use function memory_get_usage;
use function number_format;
use function sprintf;

/**
 * Class ErrorHandler
 */
class ErrorHandler
{
    /** @var Config */
    private $config;

    /** @var Logger */
    private $logger;

    /**
     * ErrorHandler constructor.
     *
     * @param Logger $logger
     * @param Config $config
     */
    public function __construct(Logger $logger, Config $config)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Handles errors by logging them
     *
     * @param int    $number
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @return void
     * @throws Exception
     */
    public function handle(
        int $number,
        string $message,
        string $file = '',
        int $line = 0
    ): void {
        $this
            ->logger
            ->error(
                sprintf(
                    '[#:%s]-[L: %s] : %s (%s)',
                    $number,
                    $line,
                    $message,
                    $file
                )
            )
        ;
    }

    /**
     * Application shutdown - logs metrics in devMode
     *
     * @return void
     * @throws Exception
     */
    public function shutdown(): void
    {
        if (true === $this->config->path('app.devMode')) {
            $memory    = number_format(memory_get_usage() / 1000000, 2);
            $execution = number_format(
                (hrtime(true) - $this->config->path('app.time')) / 1000000,
                4
            );

            $this
                ->logger
                ->info(
                    sprintf(
                        'Shutdown completed [%s]ms - [%s]MB',
                        $execution,
                        $memory
                    )
                )
            ;
        }
    }
}

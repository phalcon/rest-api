<?php

declare(strict_types=1);

namespace Niden;

use function memory_get_usage;
use function microtime;
use function number_format;
use Monolog\Logger;
use Phalcon\Config;

/**
 * Class ErrorHandler
 *
 * @package Niden
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
     */
    public function handle(int $number, string $message, string $file = '', int $line = 0)
    {
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
            );
    }

    /**
     * Application shutdown - logs metrics in devMode
     */
    public function shutdown()
    {
        if (true === $this->config->path('app.devMode')) {
            $memory    = number_format(memory_get_usage() / 1000000, 2);
            $execution = number_format(
                microtime(true) -  $this->config->path('app.time'),
                4
            );

            $this
                ->logger
                ->info(
                    sprintf(
                        'Shutdown completed [%s]s - [%s]MB',
                        $execution,
                        $memory
                    )
                );
        }
    }
}

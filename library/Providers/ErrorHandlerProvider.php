<?php

namespace Niden\Providers;

use function memory_get_usage;
use Niden\Logger;
use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class ErrorHandlerProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Logger $logger */
        $logger  = $container->getShared('logger');
        /** @var Config $registry */
        $config  = $container->getShared('config');

        date_default_timezone_set($config->path('app.timezone'));
        ini_set('display_errors', $config->path('app.devMode'));
        error_reporting(E_ALL);

        $this->registerErrorHandler($logger);
        $this->registerShutdownFunction($logger, $config);
    }

    /**
     * Registers the error handler
     *
     * @param Logger $logger
     */
    private function registerErrorHandler(Logger $logger)
    {
        set_error_handler(
            function ($errorNumber, $errorString, $errorFile, $errorLine, $errorContext) use ($logger) {
                $logger->error(
                    sprintf(
                        '[#:%s]-[L: %s] : %s (%s) %s %s',
                        $errorNumber,
                        $errorLine,
                        $errorString,
                        $errorFile,
                        PHP_EOL,
                        json_encode($errorContext)
                    )
                );
            }
        );
    }

    private function registerShutdownFunction(Logger $logger, Config $config)
    {
        register_shutdown_function(
            function () use ($logger, $config) {
                if (true === $config->path('app.devMode')) {
                    $memory    = number_format(memory_get_usage() / 1000000, 2);
                    $execution = number_format(
                        microtime(true) -  $config->path('app.time'),
                        4
                    );
                    $logger->info(
                        sprintf(
                            'Shutdown completed [%s]s - [%s]MB',
                            $execution,
                            $memory
                        )
                    );
                }
            }
        );
    }
}

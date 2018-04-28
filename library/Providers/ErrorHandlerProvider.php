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

        ini_set('display_errors', $config->path('app.devMode'));
        error_reporting(E_ALL);

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

        register_shutdown_function(
            function () use ($logger, $config) {
                if (true === $config->path('app.devMode')) {
                    $logger->info(
                        sprintf(
                            'Shutdown completed [%s]s - [%s]MB',
                            microtime(true) - $config->path('app.time'),
                            round(memory_get_usage(true) / 1048576, 2)
                        )
                    );
                }
            }
        );

        date_default_timezone_set($config->path('app.timezone'));
    }
}

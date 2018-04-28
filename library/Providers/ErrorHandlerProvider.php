<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Registry;
use Niden\Logger;
use Niden\View;

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
        $logger   = $container->getShared('logger');
        /** @var Registry $registry */
        $registry = $container->getShared('registry');

        ini_set('display_errors', $registry->offsetGet('devMode'));
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
            function () use ($logger, $registry) {
                if (true === $registry->offsetGet('devMode')) {
                    $logger->info(
                        sprintf(
                            'Shutdown completed [%s]s - [%s]MB - [%s] Queries',
                            $registry->offsetGet('execution'),
                            $registry->offsetGet('memory'),
                            $registry->offsetGet('queries')
                        )
                    );
                }
            }
        );


        date_default_timezone_set('UTC');
    }
}

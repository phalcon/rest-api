<?php

declare(strict_types=1);

namespace Baka\Providers;

use function Niden\Core\appPath;
use function Niden\Core\envValue;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class LoggerProvider implements ServiceProviderInterface
{
    /**
     * Registers the logger component
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'log',
            function () {
                /** @var string $logName */
                $logName = envValue('LOGGER_DEFAULT_FILENAME', 'api.log');
                /** @var string $logPath */
                $logPath = envValue('LOGGER_DEFAULT_PATH', 'storage/logs');
                $logFile = appPath($logPath) . '/' . $logName . '.log';

                $formatter = new LineFormatter("[%datetime%][%level_name%] %message%\n");

                $logger = new Logger('api-logger');

                $handler = new StreamHandler($logFile, Logger::DEBUG);
                $handler->setFormatter($formatter);

                //sentry logger
                $client = new Raven_Client('https://' . getenv('SENTRY_RPOJECT_SECRET') . '@sentry.io/' . getenv('SENTRY_PROJECT_ID'));
                $handlerSentry = new Monolog\Handler\RavenHandler($client, Logger::ERROR);
                $handlerSentry->setFormatter(new Monolog\Formatter\LineFormatter("%message% %context% %extra%\n"));

                $logger->pushHandler($handler);
                $logger->pushHandler($handlerSentry);

                return $logger;
            }
        );
    }
}

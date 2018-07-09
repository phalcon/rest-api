<?php

declare(strict_types=1);

namespace Niden\Providers;

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
            'logger',
            function () {
                /** @var string $logName */
                $logName   = envValue('LOGGER_DEFAULT_FILENAME', 'api.log');
                /** @var string $logPath */
                $logPath   = envValue('LOGGER_DEFAULT_PATH', 'storage/logs');
                $logFile   = appPath($logPath) . '/' . $logName . '.log';
                $formatter = new LineFormatter("[%datetime%][%level_name%] %message%\n");
                $logger    = new Logger('api-logger');
                $handler   = new StreamHandler($logFile, Logger::DEBUG);
                $handler->setFormatter($formatter);
                $logger->pushHandler($handler);

                return $logger;
            }
        );
    }
}

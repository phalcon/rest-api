<?php

declare(strict_types=1);

namespace Niden\Providers;

use function Niden\Core\appPath;
use function Niden\Core\envValue;
use Niden\Logger;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Logger\Formatter\Line;

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
                $logName   = envValue('LOGGER_DEFAULT_FILENAME', 'api.log');
                $logPath   = envValue('LOGGER_DEFAULT_PATH', 'storage/logs');
                $logFile   = appPath($logPath) . '/' . $logName . '.log';
                $formatter = new Line(
                    '[%date%][%type%] %message%',
                    'Y-m-d H:i:s'
                );

                $logger = new Logger($logFile);
                $logger->setFormatter($formatter);
                $logger->setLogLevel(Logger::DEBUG);

                return $logger;
            }
        );
    }
}

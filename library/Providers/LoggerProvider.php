<?php

namespace Niden\Providers;

use function Niden\Functions\appPath;
use Niden\Logger;
use Phalcon\Config;
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
        /** @var Config $config */
        $config  = $container->getShared('config');

        $container->setShared(
            'logger',
            function () use ($config) {
                $logFile   = $config->path('logger.path')
                           . $config->path('logger.name')
                           . '.log';
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

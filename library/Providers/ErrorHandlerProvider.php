<?php

declare(strict_types=1);

namespace Baka\Providers;

use Monolog\Logger;
use Niden\ErrorHandler;
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
        $logger = $container->getShared('log');
        /** @var Config $registry */
        $config = $container->getShared('config');

        date_default_timezone_set($config->path('app.timezone'));

        //if production?
        if (strtolower($config->app->env) == 'production') {
            ini_set('display_errors', 'Off');
        }

        error_reporting(E_ALL);

        $handler = new ErrorHandler($logger, $config);
        set_error_handler([$handler, 'handle']);

        if (strtolower($config->app->env) != 'production') {
            register_shutdown_function([$handler, 'shutdown']);
        }
    }
}

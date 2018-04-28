<?php

namespace Niden\Providers;

use function Niden\Functions\appPath;
use Niden\Exception\FileNotFoundException;
use Phalcon\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Config;

class ConfigProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     *
     * @throws FileNotFoundException
     */
    public function register(DiInterface $container)
    {
        $configFile = appPath('config/config.php');
        if (true !== file_exists($configFile)) {
            throw new FileNotFoundException(
                'Config file was not found'
            );
        }

        $container->setShared(
            'config',
            function () use ($configFile) {
                $data = require $configFile;

                return new Config($data);
            }
        );
    }
}

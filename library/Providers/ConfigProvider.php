<?php

namespace Niden\Providers;

use function Niden\Functions\appPath;
use Phalcon\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Config;

class ConfigProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'config',
            function () {
                $data = require appPath('config/config.php');

                return new Config($data);
            }
        );
    }
}

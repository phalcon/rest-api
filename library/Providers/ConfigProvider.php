<?php

declare(strict_types=1);

namespace Niden\Providers;

use function Niden\Core\appPath;
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
                $data = require appPath('library/Core/config.php');

                return new Config($data);
            }
        );
    }
}

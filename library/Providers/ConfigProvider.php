<?php

declare(strict_types=1);

namespace Phalcon\Api\Providers;

use Phalcon\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use function Phalcon\Api\Core\appPath;

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

<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Providers;

use Phalcon\Cache;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Storage\SerializerFactory;
use function Phalcon\Api\Core\appPath;

class CacheDataProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'cache',
            function () {
                /** @var array $backOptions */
                $options = include appPath('cli/config/cache.php');

                $serializer = new SerializerFactory();
                $adapter = new Cache\Adapter\Libmemcached($serializer, $options);

                return new Cache($adapter);
            }
        );
    }
}

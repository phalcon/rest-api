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

use Phalcon\Cli\Dispatcher;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class CliDispatcherProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared(
            'dispatcher',
            function () {
                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace('Phalcon\Api\Cli\Tasks');

                return $dispatcher;
            }
        );
    }
}

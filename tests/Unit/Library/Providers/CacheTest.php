<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Tests\Unit\Library\Providers;

use Phalcon\Api\Providers\CacheDataProvider;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Cache\Cache;
use Phalcon\Di\FactoryDefault;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

final class CacheTest extends AbstractUnitTestCase
{
    public function testRegistration(): void
    {
        $diContainer = new FactoryDefault();
        $config      = new ConfigProvider();
        $config->register($diContainer);
        $provider = new CacheDataProvider();
        $provider->register($diContainer);

        $this->assertTrue($diContainer->has('cache'));
        /** @var Cache $cache */
        $cache = $diContainer->getShared('cache');
        $this->assertTrue($cache instanceof Cache);
    }
}

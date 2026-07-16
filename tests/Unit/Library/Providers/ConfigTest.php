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

use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Config\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

final class ConfigTest extends AbstractUnitTestCase
{
    public function testRegistration(): void
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);

        $this->assertTrue($diContainer->has('config'));
        $config = $diContainer->getShared('config');
        $this->assertTrue($config instanceof Config);

        $configArray = $config->toArray();
        $this->assertTrue(isset($configArray['app']['version']));
        $this->assertTrue(isset($configArray['app']['timezone']));
        $this->assertTrue(isset($configArray['app']['debug']));
        $this->assertTrue(isset($configArray['app']['env']));
        $this->assertTrue(isset($configArray['app']['devMode']));
        $this->assertTrue(isset($configArray['app']['baseUri']));
        $this->assertTrue(isset($configArray['app']['supportEmail']));
        $this->assertTrue(isset($configArray['app']['time']));
        $this->assertTrue(isset($configArray['cache']));
    }
}

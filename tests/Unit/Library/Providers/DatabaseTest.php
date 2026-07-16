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
use Phalcon\Api\Providers\DatabaseProvider;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\FactoryDefault;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

final class DatabaseTest extends AbstractUnitTestCase
{
    public function testRegistration(): void
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new DatabaseProvider();
        $provider->register($diContainer);

        $this->assertTrue($diContainer->has('db'));
        /** @var Mysql $db */
        $db = $diContainer->getShared('db');
        $this->assertTrue($db instanceof Mysql);
        $this->assertSame('mysql', $db->getType());
    }
}

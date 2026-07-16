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

namespace Phalcon\Api\Tests\Unit\Config;

use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function is_array;
use function Phalcon\Api\Core\appPath;

final class ConfigTest extends AbstractUnitTestCase
{
    public function testConfig(): void
    {
        $config = require(appPath('src/Core/config.php'));

        $this->assertTrue(is_array($config));
        $this->assertTrue(isset($config['app']));
        $this->assertTrue(isset($config['cache']));
    }
}

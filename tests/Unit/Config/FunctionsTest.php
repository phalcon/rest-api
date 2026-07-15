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

use Phalcon\Api\Constants\Relationships;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\appUrl;
use function Phalcon\Api\Core\envValue;

final class FunctionsTest extends AbstractUnitTestCase
{
    private array $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = $_ENV ?? [];
    }

    protected function tearDown(): void
    {
        $_ENV = $this->store;

        parent::tearDown();
    }

    public function testAppPath(): void
    {
        $path = dirname(__FILE__, 4);

        $this->assertSame($path, appPath());
    }

    public function testAppPathWithParameter(): void
    {
        $path = dirname(__FILE__, 4) . '/library/Core/config.php';

        $this->assertSame($path, appPath('library/Core/config.php'));
    }

    public function testEnvValueAsFalse(): void
    {
        $_ENV['SOMEVAL'] = false;

        $this->assertFalse(envValue('SOMEVAL'));
    }

    public function testEnvValueAsTrue(): void
    {
        $_ENV['SOMEVAL'] = true;

        $this->assertTrue(envValue('SOMEVAL'));
    }

    public function testEnvValueWithValue(): void
    {
        $_ENV['SOMEVAL'] = 'someval';

        $this->assertSame('someval', envValue('SOMEVAL'));
    }

    public function testAppUrlWithUrl(): void
    {
        $this->assertSame(
            'http://api.phalcon.ld/companies/1',
            appUrl(Relationships::COMPANIES, 1)
        );
    }
}

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

namespace Phalcon\Api\Tests\Unit\Library\Constants;

use Phalcon\Api\Constants\Flags;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

final class FlagsTest extends AbstractUnitTestCase
{
    public function testConstants(): void
    {
        $this->assertSame(1, Flags::ACTIVE);
        $this->assertSame(2, Flags::INACTIVE);
    }
}

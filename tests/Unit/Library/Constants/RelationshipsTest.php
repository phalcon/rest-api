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

use Phalcon\Api\Constants\Relationships;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

final class RelationshipsTest extends AbstractUnitTestCase
{
    public function testConstants(): void
    {
        $this->assertSame('companies', Relationships::COMPANIES);
        $this->assertSame('individual-types', Relationships::INDIVIDUAL_TYPES);
        $this->assertSame('individuals', Relationships::INDIVIDUALS);
        $this->assertSame('product-types', Relationships::PRODUCT_TYPES);
        $this->assertSame('products', Relationships::PRODUCTS);
        $this->assertSame('users', Relationships::USERS);
    }
}

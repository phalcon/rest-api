<?php

namespace Phalcon\Api\Tests\unit\library\Constants;

use CliTester;
use Phalcon\Api\Constants\Relationships;

class RelationshipsCest
{
    public function checkConstants(CliTester $I)
    {
        $I->assertSame('companies', Relationships::COMPANIES);
        $I->assertSame('individual-types', Relationships::INDIVIDUAL_TYPES);
        $I->assertSame('individuals', Relationships::INDIVIDUALS);
        $I->assertSame('product-types', Relationships::PRODUCT_TYPES);
        $I->assertSame('products', Relationships::PRODUCTS);
        $I->assertSame('users', Relationships::USERS);
    }
}

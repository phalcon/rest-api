<?php

namespace Niden\Tests\unit\library\Constants;

use \CliTester;
use Niden\Constants\Resources;

class ResourcesCest
{
    public function checkConstants(CliTester $I)
    {
        $I->assertEquals('companies', Resources::COMPANIES);
        $I->assertEquals('individual-types', Resources::INDIVIDUAL_TYPES);
        $I->assertEquals('individuals', Resources::INDIVIDUALS);
        $I->assertEquals('product-types', Resources::PRODUCT_TYPES);
        $I->assertEquals('products', Resources::PRODUCTS);
        $I->assertEquals('users', Resources::USERS);
    }
}

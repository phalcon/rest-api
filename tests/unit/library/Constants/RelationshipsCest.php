<?php

namespace Niden\Tests\unit\library\Constants;

use \CliTester;
use Niden\Constants\Relationships;

class RelationshipsCest
{
    public function checkConstants(CliTester $I)
    {
        $I->assertEquals('company', Relationships::COMPANY);
        $I->assertEquals('individual', Relationships::INDIVIDUAL);
        $I->assertEquals('individualType', Relationships::INDIVIDUAL_TYPE);
        $I->assertEquals('individuals', Relationships::INDIVIDUALS);
        $I->assertEquals('product', Relationships::PRODUCT);
        $I->assertEquals('productType', Relationships::PRODUCT_TYPE);
        $I->assertEquals('products', Relationships::PRODUCTS);
    }
}

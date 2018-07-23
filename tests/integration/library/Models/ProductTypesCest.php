<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Constants\Relationships;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Phalcon\Filter;

class ProductTypesCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            ProductTypes::class,
            [
                'prt_id',
                'prt_name',
                'prt_description',
            ]
        );
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new ProductTypes();
        $expected = [
            'id'          => Filter::FILTER_ABSINT,
            'name'        => Filter::FILTER_STRING,
            'description' => Filter::FILTER_STRING,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new ProductTypes();
        $I->assertEquals('prt', $model->getTablePrefix());
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(ProductTypes::class);
        $expected = [
            [0, 'id', Products::class, 'typeId', ['alias' => Relationships::PRODUCT, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }
}

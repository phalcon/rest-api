<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Phalcon\Filter;

class ProductsCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Products::class,
            [
                'prd_id',
                'prd_prt_id',
                'prd_name',
                'prd_description',
                'prd_quantity',
                'prd_price',
            ]
        );
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new Products();
        $expected = [
            'id'          => Filter::FILTER_ABSINT,
            'typeId'      => Filter::FILTER_ABSINT,
            'name'        => Filter::FILTER_STRING,
            'description' => Filter::FILTER_STRING,
            'quantity'    => Filter::FILTER_ABSINT,
            'price'       => Filter::FILTER_FLOAT,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new Products();
        $I->assertEquals('prd', $model->getTablePrefix());
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(Products::class);
        $expected = [
            [1, 'typeId', ProductTypes::class, 'id', ['alias' => Relationships::PRODUCT_TYPE, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }
}

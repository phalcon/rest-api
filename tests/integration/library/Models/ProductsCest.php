<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Constants\Relationships;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Phalcon\Filter\Filter;

class ProductsCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Products::class,
            [
                'id',
                'typeId',
                'name',
                'description',
                'quantity',
                'price',
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

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(Products::class);
        $expected = [
            [0, 'typeId', ProductTypes::class, 'id', ['alias' => Relationships::PRODUCT_TYPES, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }
}

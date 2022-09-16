<?php

namespace Phalcon\Api\Tests\integration\library\Models;

use IntegrationTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Phalcon\Filter\Filter;

class ProductsCest
{
    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
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

    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
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
        $I->assertSame($expected, $model->getModelFilters());
    }

    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(Products::class);
        $expected = [
            [
                0,
                'typeId',
                ProductTypes::class,
                'id',
                ['alias' => Relationships::PRODUCT_TYPES, 'reusable' => true]
            ],
            [
                4,
                'id',
                Companies::class,
                'id',
                ['alias' => Relationships::COMPANIES, 'reusable' => true]
            ],
        ];

        $I->assertSame($expected, $actual);
    }
}

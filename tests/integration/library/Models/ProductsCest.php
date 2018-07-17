<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Models\Products;
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
            'prd_id'          => Filter::FILTER_ABSINT,
            'prd_prt_id'      => Filter::FILTER_ABSINT,
            'prd_name'        => Filter::FILTER_STRING,
            'prd_description' => Filter::FILTER_STRING,
            'prd_quantity'    => Filter::FILTER_ABSINT,
            'prd_price'       => Filter::FILTER_FLOAT,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new Products();
        $I->assertEquals('prd', $model->getTablePrefix());
    }
}

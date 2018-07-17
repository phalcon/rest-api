<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
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
            'prt_id'          => Filter::FILTER_ABSINT,
            'prt_name'        => Filter::FILTER_STRING,
            'prt_description' => Filter::FILTER_STRING,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new ProductTypes();
        $I->assertEquals('prt', $model->getTablePrefix());
    }
}

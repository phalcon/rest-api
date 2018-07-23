<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\CompaniesXProducts;
use Niden\Models\Individuals;
use Niden\Models\Products;
use Phalcon\Filter;

class CompaniesXProductsCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            CompaniesXProducts::class,
            [
                'cxp_com_id',
                'cxp_prd_id',
            ]
        );
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new CompaniesXProducts();
        $expected = [
            'cxp_com_id' => Filter::FILTER_ABSINT,
            'cxp_prd_id' => Filter::FILTER_ABSINT,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new CompaniesXProducts();
        $I->assertEquals('cxp', $model->getTablePrefix());
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(CompaniesXProducts::class);
        $expected = [
            [0, 'cxp_com_id', Companies::class, 'id', ['alias' => Relationships::COMPANY, 'reusable' => true]],
            [0, 'cxp_prd_id', Products::class, 'id', ['alias' => Relationships::PRODUCT, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }
}

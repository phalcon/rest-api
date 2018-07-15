<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Models\Companies;
use Phalcon\Filter;

class CompaniesCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Companies::class,
            [
                'com_id',
                'com_name',
                'com_address',
                'com_city',
                'com_telephone',
            ]
        );
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new Companies();
        $expected = [
            'com_id'        => Filter::FILTER_ABSINT,
            'com_name'      => Filter::FILTER_STRING,
            'com_address'   => Filter::FILTER_STRING,
            'com_city'      => Filter::FILTER_STRING,
            'com_telephone' => Filter::FILTER_STRING,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new Companies();
        $I->assertEquals('com', $model->getTablePrefix());
    }
}

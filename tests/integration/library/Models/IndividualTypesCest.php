<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Constants\Relationships;
use Niden\Models\Individuals;
use Niden\Models\IndividualTypes;
use Phalcon\Filter;

class IndividualTypesCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            IndividualTypes::class,
            [
                'id',
                'name',
                'description',
            ]
        );
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new IndividualTypes();
        $expected = [
            'id'          => Filter::FILTER_ABSINT,
            'name'        => Filter::FILTER_STRING,
            'description' => Filter::FILTER_STRING,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(IndividualTypes::class);
        $expected = [
            [2, 'id', Individuals::class, 'typeId', ['alias' => Relationships::INDIVIDUALS, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }
}

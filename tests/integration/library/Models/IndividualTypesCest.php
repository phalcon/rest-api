<?php

namespace Phalcon\Api\Tests\integration\library\Models;

use IntegrationTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
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

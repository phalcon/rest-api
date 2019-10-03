<?php

namespace Phalcon\Api\Tests\integration\library\Models;

use IntegrationTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Filter;

class IndividualsCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Individuals::class,
            [
                'id',
                'companyId',
                'typeId',
                'prefix',
                'first',
                'middle',
                'last',
                'suffix',
            ]
        );
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new Individuals();
        $expected = [
            'id'        => Filter::FILTER_ABSINT,
            'companyId' => Filter::FILTER_ABSINT,
            'typeId'    => Filter::FILTER_ABSINT,
            'prefix'    => Filter::FILTER_STRING,
            'first'     => Filter::FILTER_STRING,
            'middle'    => Filter::FILTER_STRING,
            'last'      => Filter::FILTER_STRING,
            'suffix'    => Filter::FILTER_STRING,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(Individuals::class);
        $expected = [
            [0, 'companyId', Companies::class, 'id', ['alias' => Relationships::COMPANIES, 'reusable' => true]],
            [1, 'typeId', IndividualTypes::class, 'id', ['alias' => Relationships::INDIVIDUAL_TYPES, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }
}

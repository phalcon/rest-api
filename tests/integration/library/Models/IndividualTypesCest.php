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
                'idt_id',
                'idt_name',
                'idt_description',
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

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new IndividualTypes();
        $I->assertEquals('idt', $model->getTablePrefix());
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(IndividualTypes::class);
        $expected = [
            [0, 'idt_id', Individuals::class, 'ind_idt_id', ['alias' => Relationships::INDIVIDUAL, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }
}

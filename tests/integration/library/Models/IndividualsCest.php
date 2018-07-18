<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\Individuals;
use Niden\Models\IndividualTypes;
use Phalcon\Filter;

class IndividualsCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Individuals::class,
            [
                'ind_id',
                'ind_com_id',
                'ind_idt_id',
                'ind_name_prefix',
                'ind_name_first',
                'ind_name_middle',
                'ind_name_last',
                'ind_name_suffix',
            ]
        );
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new Individuals();
        $expected = [
            'ind_id'          => Filter::FILTER_ABSINT,
            'ind_com_id'      => Filter::FILTER_ABSINT,
            'ind_idt_id'      => Filter::FILTER_ABSINT,
            'ind_name_prefix' => Filter::FILTER_STRING,
            'ind_name_first'  => Filter::FILTER_STRING,
            'ind_name_middle' => Filter::FILTER_STRING,
            'ind_name_last'   => Filter::FILTER_STRING,
            'ind_name_suffix' => Filter::FILTER_STRING,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new Individuals();
        $I->assertEquals('ind', $model->getTablePrefix());
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(Individuals::class);
        $expected = [
            [0, 'ind_com_id', Companies::class, 'com_id', ['alias' => Relationships::COMPANY, 'reusable' => true]],
            [1, 'ind_idt_id', IndividualTypes::class, 'idt_id', ['alias' => Relationships::INDIVIDUAL_TYPE, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }
}

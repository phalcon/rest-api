<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\Individuals;
use Niden\Models\Products;
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

    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(Companies::class);
        $expected = [
            [2, 'com_id', Individuals::class, 'ind_com_id', ['alias' => Relationships::INDIVIDUALS, 'reusable' => true]],
            [2, 'com_id', Products::class, 'prd_com_id', ['alias' => Relationships::PRODUCTS, 'reusable' => true]],
        ];
        $I->assertEquals($expected, $actual);
    }

    public function validateUniqueName(IntegrationTester $I)
    {
        $companyOne = new Companies();
        /** @var Companies $companyOne */
        $result  = $companyOne
                    ->set('com_name', 'acme')
                    ->set('com_address', '123 Phalcon way')
                    ->set('com_city', 'World')
                    ->set('com_telephone', '555-999-4444')
                    ->save()
        ;
        $I->assertNotEquals(false, $result);

        $companyTwo = new Companies();
        /** @var Companies $companyTwo */
        $result  = $companyTwo
            ->set('com_name', 'acme')
            ->set('com_address', '123 Phalcon way')
            ->set('com_city', 'World')
            ->set('com_telephone', '555-999-4444')
            ->save()
        ;
        $I->assertEquals(false, $result);
        $I->assertEquals(1, count($companyTwo->getMessages()));

        $messages = $companyTwo->getMessages();
        $I->assertEquals('The company name already exists in the database', $messages[0]->getMessage());
        $result = $companyOne->delete();
        $I->assertNotEquals(false, $result);
    }
}

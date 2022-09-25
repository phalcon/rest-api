<?php

namespace Phalcon\Api\Tests\integration\library\Models;

use IntegrationTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\Products;
use Phalcon\Filter\Filter;

class CompaniesCest
{
    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Companies::class,
            [
                'id',
                'name',
                'address',
                'city',
                'phone',
            ]
        );
    }

    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
    public function validateFilters(IntegrationTester $I)
    {
        $model    = new Companies();
        $expected = [
            'id'      => Filter::FILTER_ABSINT,
            'name'    => Filter::FILTER_STRING,
            'address' => Filter::FILTER_STRING,
            'city'    => Filter::FILTER_STRING,
            'phone'   => Filter::FILTER_STRING,
        ];
        $I->assertSame($expected, $model->getModelFilters());
    }

    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
    public function validateRelationships(IntegrationTester $I)
    {
        $actual   = $I->getModelRelationships(Companies::class);
        $expected = [
            [
                2,
                'id',
                Individuals::class,
                'companyId',
                ['alias' => Relationships::INDIVIDUALS, 'reusable' => true]
            ],
            [
                4,
                'id',
                Products::class,
                'id',
                ['alias' => Relationships::PRODUCTS, 'reusable' => true]
            ],
        ];

        $I->assertSame($expected, $actual);
    }

    /**
     * @param IntegrationTester $I
     *
     * @return void
     * @throws ModelException
     */
    public function validateUniqueName(IntegrationTester $I)
    {
        $companyOne = new Companies();
        /** @var Companies $companyOne */
        $result = $companyOne
            ->set('name', 'acme')
            ->set('address', '123 Phalcon way')
            ->set('city', 'World')
            ->set('phone', '555-999-4444')
            ->save()
        ;
        $I->assertNotEquals(false, $result);

        $companyTwo = new Companies();
        /** @var Companies $companyTwo */
        $result = $companyTwo
            ->set('name', 'acme')
            ->set('address', '123 Phalcon way')
            ->set('city', 'World')
            ->set('phone', '555-999-4444')
            ->save()
        ;
        $I->assertSame(false, $result);
        $I->assertSame(1, count($companyTwo->getMessages()));

        $messages = $companyTwo->getMessages();
        $expected = 'The company name already exists in the database';
        $actual   = $messages[0]->getMessage();
        $I->assertSame($expected, $actual);

        $result = $companyOne->delete();
        $I->assertNotEquals(false, $result);
    }
}

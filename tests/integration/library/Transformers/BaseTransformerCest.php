<?php

namespace Phalcon\Api\Tests\integration\library\Transformers;

use IntegrationTester;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Transformers\BaseTransformer;

class BaseTransformerCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function checkTransformer(IntegrationTester $I)
    {
        /** @var Companies $company */
        $company = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => 'acme',
                'address' => '123 Phalcon way',
                'city'    => 'World',
                'phone'   => '555-999-4444',
            ]
        );

        $transformer = new BaseTransformer();
        $expected    = [
            'id'      => $company->get('id'),
            'name'    => $company->get('name'),
            'address' => $company->get('address'),
            'city'    => $company->get('city'),
            'phone'   => $company->get('phone'),
        ];

        $I->assertEquals($expected, $transformer->transform($company));
    }
}

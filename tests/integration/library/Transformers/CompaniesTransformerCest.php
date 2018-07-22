<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use Niden\Constants\Resources;
use function Niden\Core\envValue;
use Niden\Models\Companies;
use Niden\Transformers\CompaniesTransformer;

class CompaniesTransformerCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function checkTransformer(IntegrationTester $I)
    {
        /** @var Companies $company */
        $company = $I->haveRecordWithFields(
            Companies::class,
            [
                'com_name'      => 'acme',
                'com_address'   => '123 Phalcon way',
                'com_city'      => 'World',
                'com_telephone' => '555-999-4444',
            ]
        );

        $transformer = new CompaniesTransformer();
        $expected = [
            'id'         => $company->get('com_id'),
            'type'       => Resources::COMPANIES,
            'attributes' => [
                'name'    => $company->get('com_name'),
                'address' => $company->get('com_address'),
                'city'    => $company->get('com_city'),
                'phone'   => $company->get('com_telephone'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/companies/%s',
                    envValue('APP_URL', 'localhost'),
                    $company->get('com_id')
                ),
            ]
        ];

        $I->assertEquals($expected, $transformer->transform($company));
    }
}

<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use Niden\Constants\Resources;
use function Niden\Core\envValue;
use Niden\Models\ProductTypes;
use Niden\Transformers\ProductTypesTransformer;
use function uniqid;

class ProductTypesTransformerCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function checkTransformer(IntegrationTester $I)
    {
        /** @var ProductTypes $type */
        $type = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'prt_name'        => uniqid('type-n-'),
                'prt_description' => uniqid('type-d-'),
            ]
        );

        $transformer = new ProductTypesTransformer();
        $expected    = [
            'id'         => $type->get('prt_id'),
            'type'       => Resources::PRODUCT_TYPES,
            'attributes' => [
                'name'        => $type->get('prt_name'),
                'description' => $type->get('prt_description'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/producttypes/%s',
                    envValue('APP_URL', 'localhost'),
                    $type->get('prt_id')
                ),
            ]
        ];

        $I->assertEquals($expected, $transformer->transform($type));
    }
}

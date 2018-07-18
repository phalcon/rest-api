<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
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
                'prt_name' => uniqid('type'),
            ]
        );

        $transformer = new ProductTypesTransformer();
        $expected    = [
            'id'   => $type->get('prt_id'),
            'name' => $type->get('prt_name'),
        ];

        $I->assertEquals($expected, $transformer->transform($type));
    }
}

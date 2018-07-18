<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use Niden\Models\ProductTypes;
use Niden\Transformers\TypesTransformer;
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

        $transformer = new TypesTransformer();
        $expected    = [
            'id'          => $type->get('prt_id'),
            'name'        => $type->get('prt_name'),
            'description' => $type->get('prt_description'),
        ];

        $I->assertEquals($expected, $transformer->transform($type));
    }
}

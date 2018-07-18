<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use Niden\Constants\Resources;
use Niden\Models\IndividualTypes;
use Niden\Transformers\TypesTransformer;
use function uniqid;

class IndividualTypesTransformerCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function checkTransformer(IntegrationTester $I)
    {
        /** @var IndividualTypes $type */
        $type = $I->haveRecordWithFields(
            IndividualTypes::class,
            [
                'idt_name'        => uniqid('type-n-'),
                'idt_description' => uniqid('type-d-'),
            ]
        );

        $transformer = new TypesTransformer();
        $expected    = [
            'id'         => $type->get('idt_id'),
            'type'       => Resources::INDIVIDUAL_TYPES,
            'attributes' => [
                'name'        => $type->get('idt_name'),
                'description' => $type->get('idt_description'),
            ],
        ];

        $I->assertEquals($expected, $transformer->transform($type));
    }
}

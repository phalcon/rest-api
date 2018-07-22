<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use Niden\Constants\Resources;
use function Niden\Core\envValue;
use Niden\Models\IndividualTypes;
use Niden\Transformers\IndividualTypesTransformer;
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

        $transformer = new IndividualTypesTransformer();
        $expected    = [
            'id'         => $type->get('idt_id'),
            'type'       => Resources::INDIVIDUAL_TYPES,
            'attributes' => [
                'name'        => $type->get('idt_name'),
                'description' => $type->get('idt_description'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/individualtypes/%s',
                    envValue('APP_URL', 'localhost'),
                    $type->get('idt_id')
                ),
            ]
        ];

        $I->assertEquals($expected, $transformer->transform($type));
    }
}

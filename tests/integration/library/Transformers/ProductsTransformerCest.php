<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use Niden\Constants\Resources;
use function Niden\Core\envValue;
use Niden\Models\Products;
use Niden\Transformers\ProductsTransformer;

class ProductsTransformerCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function checkTransformer(IntegrationTester $I)
    {
        /** @var Products $product */
        $product = $I->haveRecordWithFields(
            Products::class,
            [
                'prd_name'        => 'test product',
                'prd_description' => 'test product description',
                'prd_quantity'    => 25,
                'prd_price'       => 19.99,
            ]
        );

        $transformer = new ProductsTransformer();
        $expected = [
            'id'         => $product->get('prd_id'),
            'type'       => Resources::PRODUCTS,
            'attributes' => [
                'name'        => $product->get('prd_name'),
                'description' => $product->get('prd_description'),
                'quantity'    => $product->get('prd_quantity'),
                'price'       => $product->get('prd_price'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/products/%s',
                    envValue('APP_URL', 'localhost'),
                    $product->get('prd_id')
                ),
            ]
        ];

        $I->assertEquals($expected, $transformer->transform($product));
    }
}

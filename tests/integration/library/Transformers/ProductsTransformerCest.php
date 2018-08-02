<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\JsonApiSerializer;
use Niden\Constants\Relationships;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Niden\Transformers\ProductsTransformer;
use function Niden\Core\envValue;
use Page\Data;

class ProductsTransformerCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function checkTransformer(IntegrationTester $I)
    {
        /** @var ProductTypes $productType */
        $productType = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name'        => 'my type',
                'description' => 'description of my type',
            ]
        );

        /** @var Products $product */
        $product = $I->haveRecordWithFields(
            Products::class,
            [
                'name'        => 'my product',
                'typeId'      => $productType->get('id'),
                'description' => 'my product description',
                'quantity'    => 99,
                'price'       => 19.99,
            ]
        );

        $url     = envValue('APP_URL', 'http://localhost');
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer($url));
        $manager->parseIncludes(Relationships::PRODUCT_TYPES);
        $resource = new Collection([$product], new ProductsTransformer(), Relationships::PRODUCTS);
        $results  = $manager->createData($resource)->toArray();
        $expected = [
            'data'     => [
                [
                    'type'          => Relationships::PRODUCTS,
                    'id'            => $product->get('id'),
                    'attributes'    => [
                        'typeId'      => $productType->get('id'),
                        'name'        => $product->get('name'),
                        'description' => $product->get('description'),
                        'quantity'    => $product->get('quantity'),
                        'price'       => $product->get('price'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            $url,
                            Relationships::PRODUCTS,
                            $product->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::PRODUCT_TYPES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    $url,
                                    Relationships::PRODUCTS,
                                    $product->get('id'),
                                    Relationships::PRODUCT_TYPES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    $url,
                                    Relationships::PRODUCTS,
                                    $product->get('id'),
                                    Relationships::PRODUCT_TYPES
                                ),
                            ],
                            'data'  => [
                                'type' => Relationships::PRODUCT_TYPES,
                                'id'   => $productType->get('id'),
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                Data::productTypeResponse($productType),
            ],
        ];

        $I->assertEquals($expected, $results);
    }
}

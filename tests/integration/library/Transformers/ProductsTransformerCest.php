<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\JsonApiSerializer;
use Niden\Constants\Resources;
use function Niden\Core\envValue;
use Niden\Models\Companies;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Niden\Transformers\BaseTransformer;
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

        $url      = envValue('APP_URL', 'http://localhost');
        $manager  = new Manager();
        $manager->setSerializer(new JsonApiSerializer($url));
        $manager->parseIncludes(Resources::PRODUCT_TYPES);
        $resource = new Collection([$product], new ProductsTransformer(), Resources::PRODUCTS);
        $results  = $manager->createData($resource)->toArray();
        $expected = [
            'data' => [
                [
                    'type'        => Resources::PRODUCTS,
                    'id'          => $product->get('id'),
                    'attributes'  => [
                        'typeId'      => $productType->get('id'),
                        'name'        => $product->get('name'),
                        'description' => $product->get('description'),
                        'quantity'    => $product->get('quantity'),
                        'price'       => $product->get('price'),
                    ],
                    'links'       => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            $url,
                            Resources::PRODUCTS,
                            $product->get('id')
                        ),
                    ],
                    'relationships' => [
                        Resources::PRODUCT_TYPES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    $url,
                                    Resources::PRODUCTS,
                                    $product->get('id'),
                                    Resources::PRODUCT_TYPES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    $url,
                                    Resources::PRODUCTS,
                                    $product->get('id'),
                                    Resources::PRODUCT_TYPES
                                ),
                            ],
                            'data'  => [
                                'type' => Resources::PRODUCT_TYPES,
                                'id'   => $productType->get('id'),
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'type'       => Resources::PRODUCT_TYPES,
                    'id'         => $productType->get('id'),
                    'attributes' => [
                        'name'        => $productType->get('name'),
                        'description' => $productType->get('description'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            $url,
                            Resources::PRODUCT_TYPES,
                            $productType->get('id')
                        ),
                    ],
                ],
            ],
        ];

        $I->assertEquals($expected, $results);
    }
}

<?php

namespace Phalcon\Api\Tests\integration\library\Transformers;

use IntegrationTester;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\JsonApiSerializer;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\CompaniesXProducts;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Phalcon\Api\Transformers\ProductsTransformer;
use function Phalcon\Api\Core\envValue;
use Page\Data;

class ProductsTransformerCest
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
                'name'    => uniqid('com-a-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );

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

        /** @var CompaniesXProducts $glue */
        $glue = $I->haveRecordWithFields(
            CompaniesXProducts::class,
            [
                'companyId' => $company->get('id'),
                'productId' => $product->get('id'),
            ]
        );

        $url     = envValue('APP_URL', 'http://localhost');
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer($url));
        $manager->parseIncludes([Relationships::COMPANIES, Relationships::PRODUCT_TYPES]);
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
                        Relationships::COMPANIES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    $url,
                                    Relationships::PRODUCTS,
                                    $product->get('id'),
                                    Relationships::COMPANIES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    $url,
                                    Relationships::PRODUCTS,
                                    $product->get('id'),
                                    Relationships::COMPANIES
                                ),
                            ],
                            'data'  => [
                                [
                                    'type' => Relationships::COMPANIES,
                                    'id'   => $company->get('id'),
                                ],
                            ],
                        ],
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
                Data::companiesResponse($company),
                Data::productTypeResponse($productType),
            ],
        ];

        $I->assertEquals($expected, $results);
    }
}

<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Tests\Integration\Library\Transformers;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\JsonApiSerializer;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\CompaniesXProducts;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Api\Transformers\ProductsTransformer;

use function uniqid;

final class ProductsTransformerTest extends AbstractIntegrationTestCase
{
    /**
     * @throws ModelException
     */
    public function testTransformer(): void
    {
        /** @var Companies $company */
        $company = $this->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-a-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );

        /** @var ProductTypes $productType */
        $productType = $this->haveRecordWithFields(
            ProductTypes::class,
            [
                'name'        => 'my type',
                'description' => 'description of my type',
            ]
        );

        /** @var Products $product */
        $product = $this->haveRecordWithFields(
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
        $glue = $this->haveRecordWithFields(
            CompaniesXProducts::class,
            [
                'companyId' => $company->get('id'),
                'productId' => $product->get('id'),
            ]
        );

        $url     = $this->getBaseUrl();
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer($url));
        $manager->parseIncludes([Relationships::COMPANIES, Relationships::PRODUCT_TYPES]);
        $resource = new Collection([$product], new ProductsTransformer(), Relationships::PRODUCTS);
        $results  = $manager->createData($resource)
                            ->toArray()
        ;
        $expected = [
            'data'     => [
                Data::productResponse($product) + [
                    'relationships' => [
                        Relationships::COMPANIES     => [
                            'links' => Data::relationshipLinks(
                                Relationships::PRODUCTS,
                                $product->get('id'),
                                Relationships::COMPANIES
                            ),
                            'data'  => [
                                [
                                    'type' => Relationships::COMPANIES,
                                    'id'   => (string) $company->get('id'),
                                ],
                            ],
                        ],
                        Relationships::PRODUCT_TYPES => [
                            'links' => Data::relationshipLinks(
                                Relationships::PRODUCTS,
                                $product->get('id'),
                                Relationships::PRODUCT_TYPES
                            ),
                            'data'  => [
                                'type' => Relationships::PRODUCT_TYPES,
                                'id'   => (string) $productType->get('id'),
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                Data::companiesResponse($company),
                /**
                 * The included type carries its own relationships now that the
                 * include uses ProductTypesTransformer rather than the base one
                 * - which is what makes `?includes=product-types.products`
                 * resolvable.
                 */
                Data::productTypeResponse($productType) + [
                    'relationships' => [
                        Relationships::PRODUCTS => [
                            'links' => Data::relationshipLinks(
                                Relationships::PRODUCT_TYPES,
                                $productType->get('id'),
                                Relationships::PRODUCTS
                            ),
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $results);
    }
}

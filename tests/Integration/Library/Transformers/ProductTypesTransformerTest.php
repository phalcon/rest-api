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

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Transformers\ProductTypesTransformer;

/**
 * Goes through FractalTrait::format() rather than building the Manager by hand,
 * because that is the path the controllers actually take.
 */
final class ProductTypesTransformerTest extends AbstractIntegrationTestCase
{
    use FractalTrait;

    /**
     * @throws ModelException
     */
    public function testTransformer(): void
    {
        $prdType = $this->addProductTypeRecord('prt-a-');
        $product = $this->addProductRecord('prd-a-', $prdType->get('id'));

        $results = $this->format(
            'collection',
            [$prdType],
            ProductTypesTransformer::class,
            Relationships::PRODUCT_TYPES,
            [Relationships::PRODUCTS]
        );

        $element = Data::productTypeResponse($prdType) + [
            'relationships' => [
                Relationships::PRODUCTS => [
                    'links' => Data::relationshipLinks(
                        Relationships::PRODUCT_TYPES,
                        $prdType->get('id'),
                        Relationships::PRODUCTS
                    ),
                    'data'  => [
                        [
                            'type' => Relationships::PRODUCTS,
                            'id'   => (string) $product->get('id'),
                        ],
                    ],
                ],
            ],
        ];

        $includedProduct = Data::productResponse($product) + [
            'relationships' => [
                Relationships::COMPANIES     => [
                    'links' => Data::relationshipLinks(
                        Relationships::PRODUCTS,
                        $product->get('id'),
                        Relationships::COMPANIES
                    ),
                ],
                Relationships::PRODUCT_TYPES => [
                    'links' => Data::relationshipLinks(
                        Relationships::PRODUCTS,
                        $product->get('id'),
                        Relationships::PRODUCT_TYPES
                    ),
                ],
            ],
        ];

        $expected = [
            'data'     => [$element],
            'included' => [$includedProduct],
        ];

        $this->assertSame($expected, $results);
    }
}

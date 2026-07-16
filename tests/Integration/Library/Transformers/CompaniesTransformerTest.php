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
use Phalcon\Api\Transformers\CompaniesTransformer;

/**
 * Goes through FractalTrait::format() rather than building the Manager by hand,
 * because that is the path the controllers actually take.
 */
final class CompaniesTransformerTest extends AbstractIntegrationTestCase
{
    use FractalTrait;

    /**
     * @throws ModelException
     */
    public function testTransformer(): void
    {
        $company    = $this->addCompanyRecord('com-a-');
        $indType    = $this->addIndividualTypeRecord('type-a-');
        $individual = $this->addIndividualRecord('ind-a-', $company->get('id'), $indType->get('id'));
        $prdType    = $this->addProductTypeRecord('prt-a-');
        $product    = $this->addProductRecord('prd-a-', $prdType->get('id'));

        $this->addCompanyXProduct($company->get('id'), $product->get('id'));

        $results = $this->format(
            'collection',
            [$company],
            CompaniesTransformer::class,
            Relationships::COMPANIES,
            [Relationships::PRODUCTS, Relationships::INDIVIDUALS]
        );

        /**
         * companiesResponse() carries the relationship links; asking for the
         * includes is what adds the `data` identifiers alongside them.
         */
        $element = Data::companiesResponse($company);

        $element['relationships'][Relationships::PRODUCTS]['data']    = [
            [
                'type' => Relationships::PRODUCTS,
                'id'   => (string) $product->get('id'),
            ],
        ];
        $element['relationships'][Relationships::INDIVIDUALS]['data'] = [
            [
                'type' => Relationships::INDIVIDUALS,
                'id'   => (string) $individual->get('id'),
            ],
        ];

        /**
         * Each included resource advertises its own relationships, because its
         * transformer declares availableIncludes of its own.
         */
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

        $includedIndividual = Data::individualResponse($individual) + [
            'relationships' => [
                Relationships::COMPANIES        => [
                    'links' => Data::relationshipLinks(
                        Relationships::INDIVIDUALS,
                        $individual->get('id'),
                        Relationships::COMPANIES
                    ),
                ],
                Relationships::INDIVIDUAL_TYPES => [
                    'links' => Data::relationshipLinks(
                        Relationships::INDIVIDUALS,
                        $individual->get('id'),
                        Relationships::INDIVIDUAL_TYPES
                    ),
                ],
            ],
        ];

        $expected = [
            'data'     => [$element],
            'included' => [$includedProduct, $includedIndividual],
        ];

        $this->assertSame($expected, $results);
    }
}

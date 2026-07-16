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
use Phalcon\Api\Transformers\IndividualTypesTransformer;

/**
 * Goes through FractalTrait::format() rather than building the Manager by hand,
 * because that is the path the controllers actually take.
 */
final class IndividualTypesTransformerTest extends AbstractIntegrationTestCase
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

        $results = $this->format(
            'collection',
            [$indType],
            IndividualTypesTransformer::class,
            Relationships::INDIVIDUAL_TYPES,
            [Relationships::INDIVIDUALS]
        );

        $element = Data::individualTypeResponse($indType) + [
            'relationships' => [
                Relationships::INDIVIDUALS => [
                    'links' => Data::relationshipLinks(
                        Relationships::INDIVIDUAL_TYPES,
                        $indType->get('id'),
                        Relationships::INDIVIDUALS
                    ),
                    'data'  => [
                        [
                            'type' => Relationships::INDIVIDUALS,
                            'id'   => (string) $individual->get('id'),
                        ],
                    ],
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
            'included' => [$includedIndividual],
        ];

        $this->assertSame($expected, $results);
    }
}

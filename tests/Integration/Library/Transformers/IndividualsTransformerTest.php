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
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Api\Transformers\IndividualsTransformer;

use function Phalcon\Api\Core\envValue;
use function sprintf;
use function uniqid;

final class IndividualsTransformerTest extends AbstractIntegrationTestCase
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

        /** @var IndividualTypes $individualType */
        $individualType = $this->haveRecordWithFields(
            IndividualTypes::class,
            [
                'name'        => 'my type',
                'description' => 'description of my type',
            ]
        );

        /** @var Individuals $individual */
        $individual = $this->haveRecordWithFields(
            Individuals::class,
            [
                'companyId' => $company->get('id'),
                'typeId'    => $individualType->get('id'),
                'prefix'    => uniqid(),
                'first'     => uniqid('first-'),
                'middle'    => uniqid(),
                'last'      => uniqid('last-'),
                'suffix'    => uniqid(),
            ]
        );

        $url     = envValue('APP_URL', 'http://localhost');
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer($url));
        $manager->parseIncludes([Relationships::COMPANIES, Relationships::INDIVIDUAL_TYPES]);
        $resource = new Collection([$individual], new IndividualsTransformer(), Relationships::INDIVIDUALS);
        $results  = $manager->createData($resource)
                            ->toArray()
        ;
        $expected = [
            'data'     => [
                [
                    'type'          => Relationships::INDIVIDUALS,
                    'id'            => (string) $individual->get('id'),
                    'attributes'    => [
                        'companyId' => $individual->get('companyId'),
                        'typeId'    => $individual->get('typeId'),
                        'prefix'    => $individual->get('prefix'),
                        'first'     => $individual->get('first'),
                        'middle'    => $individual->get('middle'),
                        'last'      => $individual->get('last'),
                        'suffix'    => $individual->get('suffix'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            $url,
                            Relationships::INDIVIDUALS,
                            $individual->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::COMPANIES        => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    $url,
                                    Relationships::INDIVIDUALS,
                                    $individual->get('id'),
                                    Relationships::COMPANIES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    $url,
                                    Relationships::INDIVIDUALS,
                                    $individual->get('id'),
                                    Relationships::COMPANIES
                                ),
                            ],
                            'data'  => [
                                'type' => Relationships::COMPANIES,
                                'id'   => (string) $company->get('id'),
                            ],
                        ],
                        Relationships::INDIVIDUAL_TYPES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    $url,
                                    Relationships::INDIVIDUALS,
                                    $individual->get('id'),
                                    Relationships::INDIVIDUAL_TYPES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    $url,
                                    Relationships::INDIVIDUALS,
                                    $individual->get('id'),
                                    Relationships::INDIVIDUAL_TYPES
                                ),
                            ],
                            'data'  => [
                                'type' => Relationships::INDIVIDUAL_TYPES,
                                'id'   => (string) $individualType->get('id'),
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                Data::companiesResponse($company),
                /**
                 * The included type carries its own relationships now that the
                 * include uses IndividualTypesTransformer rather than the base
                 * one - which is what makes `?includes=individual-types.individuals`
                 * resolvable.
                 */
                Data::individualTypeResponse($individualType) + [
                    'relationships' => [
                        Relationships::INDIVIDUALS => [
                            'links' => Data::relationshipLinks(
                                Relationships::INDIVIDUAL_TYPES,
                                $individualType->get('id'),
                                Relationships::INDIVIDUALS
                            ),
                        ],
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $results);
    }
}

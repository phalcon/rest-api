<?php

namespace Phalcon\Api\Tests\integration\library\Transformers;

use IntegrationTester;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\JsonApiSerializer;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Api\Transformers\IndividualsTransformer;
use Page\Data;
use function Phalcon\Api\Core\envValue;
use function uniqid;

class IndividualsTransformerCest
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

        /** @var IndividualTypes $individualType */
        $individualType = $I->haveRecordWithFields(
            IndividualTypes::class,
            [
                'name'        => 'my type',
                'description' => 'description of my type',
            ]
        );

        /** @var Individuals $individual */
        $individual = $I->haveRecordWithFields(
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
        $results  = $manager->createData($resource)->toArray();
        $expected = [
            'data'     => [
                [
                    'type'          => Relationships::INDIVIDUALS,
                    'id'            => $individual->get('id'),
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
                        Relationships::COMPANIES     => [
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
                                'id'   => $company->get('id'),
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
                                'id'   => $individualType->get('id'),
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                Data::companiesResponse($company),
                Data::individualTypeResponse($individualType),
            ],
        ];

        $I->assertEquals($expected, $results);
    }
}

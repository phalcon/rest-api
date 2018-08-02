<?php

namespace Niden\Tests\api\Individuals;

use ApiTester;
use Niden\Constants\Relationships;
use function Niden\Core\envValue;
use Niden\Models\Companies;
use Niden\Models\Individuals;
use Niden\Models\IndividualTypes;
use Page\Data;
use function sprintf;
use function uniqid;

class GetCest
{
    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividual(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

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
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$individualsRecordUrl, $individual->get('id')));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::individualResponse($individual),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getUnknownIndividual(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$individualsRecordUrl, 9999));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividuals(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $individualOne = $I->haveRecordWithFields(
            Individuals::class,
            [
                'companyId' => 1,
                'typeId'    => 1,
                'prefix'    => uniqid(),
                'first'     => uniqid('first-a-'),
                'middle'    => uniqid(),
                'last'      => uniqid('last-'),
                'suffix'    => uniqid(),
            ]
        );
        $individualTwo = $I->haveRecordWithFields(
            Individuals::class,
            [
                'companyId' => 1,
                'typeId'    => 1,
                'prefix'    => uniqid(),
                'first'     => uniqid('first-b-'),
                'middle'    => uniqid(),
                'last'      => uniqid('last-'),
                'suffix'    => uniqid(),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$individualsUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($individualOne),
                Data::companyResponse($individualTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getIndividualsWithRelationshipCompanies(ApiTester $I)
    {
        $this->runIndividualsWithCompaniesTests($I, Data::$individualsRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     */
    public function getIndividualsWithCompanies(ApiTester $I)
    {
        $this->runIndividualsWithCompaniesTests($I, Data::$individualsRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividualsWithUnknownRelationship(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

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

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$individualsRecordRelationshipUrl, $individual->get('id'), 'unknown'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    /**
     * @param ApiTester $I
     */
    public function getIndividualsNoData(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$individualsUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }

    private function runIndividualsWithCompaniesTests(ApiTester $I, $url = '')
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

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
                'first'     => uniqid('first-a-'),
                'middle'    => uniqid(),
                'last'      => uniqid('last-'),
                'suffix'    => uniqid(),
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $individual->get('id'),
            Relationships::COMPANIES
            )
        );
        $I->deleteHeader('Authorization');

        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'id'         => $individual->get('id'),
                    'type'       => Relationships::INDIVIDUALS,
                    'attributes' => [
                        'companyId' => $individual->get('companyId'),
                        'typeId'    => $individual->get('typeId'),
                        'prefix'    => $individual->get('prefix'),
                        'first'     => $individual->get('first'),
                        'middle'    => $individual->get('middle'),
                        'last'      => $individual->get('last'),
                        'suffix'    => $individual->get('suffix'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL'),
                            Relationships::INDIVIDUALS,
                            $individual->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::COMPANIES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL'),
                                    Relationships::INDIVIDUALS,
                                    $company->get('id'),
                                    Relationships::COMPANIES

                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL'),
                                    Relationships::INDIVIDUALS,
                                    $company->get('id'),
                                    Relationships::COMPANIES
                                ),
                            ],
                            'data' => [
                                'type' => Relationships::COMPANIES,
                                'id'   => $company->get('id'),
                            ]
                        ],
                        Relationships::INDIVIDUAL_TYPES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL'),
                                    Relationships::INDIVIDUALS,
                                    $individualType->get('id'),
                                    Relationships::INDIVIDUAL_TYPES

                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL'),
                                    Relationships::INDIVIDUALS,
                                    $individualType->get('id'),
                                    Relationships::INDIVIDUAL_TYPES
                                ),
                            ],
                            'data' => [
                                'type' => Relationships::INDIVIDUAL_TYPES,
                                'id'   => $individualType->get('id'),
                            ]
                        ]
                    ]
                ]
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::companyResponse($company),
                Data::individualTypeResponse($individualType),
            ]
        );
    }
}


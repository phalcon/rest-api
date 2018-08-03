<?php

namespace Niden\Tests\api\Individuals;

use ApiTester;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\Individuals;
use Niden\Models\IndividualTypes;
use Page\Data;
use function Niden\Core\envValue;

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
        $company        = $I->addCompanyRecord('com-a-');
        /** @var IndividualTypes $individualType */
        $individualType = $I->addIndividualTypeRecord('prt-a-');
        /** @var Individuals $individual */
        $individual     = $I->addIndividualRecord('prd-a-', $company->get('id'), $individualType->get('id'));
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
        $I->sendGET(sprintf(Data::$individualsRecordUrl, 1));
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

        /** @var Companies $company */
        $company        = $I->addCompanyRecord('com-a-');
        /** @var IndividualTypes $individualType */
        $individualType = $I->addIndividualTypeRecord('prt-a-');
        /** @var Individuals $individualOne */
        $individualOne  = $I->addIndividualRecord('ind-a-', $company->get('id'), $individualType->get('id'));
        /** @var Individuals $individualTwo */
        $individualTwo  = $I->addIndividualRecord('ind-b-', $company->get('id'), $individualType->get('id'));

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$individualsUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::individualResponse($individualOne),
                Data::individualResponse($individualTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividualsWithAllRelationships(ApiTester $I)
    {
        $this->runIndividualsWithAllRelationshipsTests($I, Data::$individualsRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividualsWithRelationshipAllRelationships(ApiTester $I)
    {
        $this->runIndividualsWithAllRelationshipsTests($I, Data::$individualsRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividualsWithCompanies(ApiTester $I)
    {
        $this->runIndividualsWithCompaniesTests($I, Data::$individualsRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividualsWithRelationshipCompanies(ApiTester $I)
    {
        $this->runIndividualsWithCompaniesTests($I, Data::$individualsRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividualsWithIndividualTypes(ApiTester $I)
    {
        $this->runIndividualsWithIndividualTypesTests($I, Data::$individualsRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividualsWithRelationshipIndividualTypes(ApiTester $I)
    {
        $this->runIndividualsWithIndividualTypesTests($I, Data::$individualsRecordRelationshipUrl);
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

    private function addRecords(ApiTester $I): array
    {
        /** @var Companies $company */
        $company        = $I->addCompanyRecord('com-a');
        /** @var IndividualTypes $individualType */
        $individualType = $I->addIndividualTypeRecord('prt-a-');
        /** @var Individuals $individual */
        $individual     = $I->addIndividualRecord('ind-a-', $company->get('id'),  $individualType->get('id'));


        return [$individual, $individualType, $company];
    }

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runIndividualsWithAllRelationshipsTests(ApiTester $I, $url)
    {
        list($individual, $individualType, $company) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $individual->get('id'),
                Relationships::COMPANIES . ',' . Relationships::INDIVIDUAL_TYPES
            )
        );

        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
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
                            envValue('APP_URL', 'localhost'),
                            Relationships::INDIVIDUALS,
                            $individual->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::COMPANIES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::INDIVIDUALS,
                                    $individual->get('id'),
                                    Relationships::COMPANIES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL', 'localhost'),
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
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::INDIVIDUALS,
                                    $individual->get('id'),
                                    Relationships::INDIVIDUAL_TYPES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL', 'localhost'),
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

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runIndividualsWithCompaniesTests(ApiTester $I, $url)
    {
        list($individual, $individualType, $company) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

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
                            envValue('APP_URL', 'localhost'),
                            Relationships::INDIVIDUALS,
                            $individual->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::COMPANIES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::INDIVIDUALS,
                                    $individual->get('id'),
                                    Relationships::COMPANIES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL', 'localhost'),
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
                    ],
                ],
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::companyResponse($company),
            ]
        );
    }

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runIndividualsWithIndividualTypesTests(ApiTester $I, $url)
    {
        list($individual, $individualType) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $individual->get('id'),
                Relationships::INDIVIDUAL_TYPES
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
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
                            envValue('APP_URL', 'localhost'),
                            Relationships::INDIVIDUALS,
                            $individual->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::INDIVIDUAL_TYPES => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::INDIVIDUALS,
                                    $individual->get('id'),
                                    Relationships::INDIVIDUAL_TYPES
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL', 'localhost'),
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
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::individualTypeResponse($individualType),
            ]
        );
    }
}

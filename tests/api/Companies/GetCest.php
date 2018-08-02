<?php

namespace Niden\Tests\api\Companies;

use ApiTester;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Page\Data;
use function Niden\Core\envValue;

class GetCest
{
    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompany(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();
        
        $company = $I->addCompanyRecord('com-a-');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesRecordUrl, $company->get('id')));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($company),
            ]
        );
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompanyUnknownRelationship(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $company = $I->addCompanyRecord('com-a-');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesRecordRelationshipUrl, $company->get('id'), 'unknown'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    /**
     * @param ApiTester $I
     */
    public function getUnknownCompany(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesRecordUrl, 1));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompanies(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var Companies $comOne */
        $comOne  = $I->addCompanyRecord('com-a-');
        /** @var Companies $comTwo */
        $comTwo  = $I->addCompanyRecord('com-b-');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($comOne),
                Data::companyResponse($comTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompaniesWithAllRelationships(ApiTester $I)
    {
        $this->runCompaniesWithAllRelationshipsTests($I, Data::$companiesRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompaniesWithRelationshipAllRelationships(ApiTester $I)
    {
        $this->runCompaniesWithAllRelationshipsTests($I, Data::$companiesRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompaniesWithIndividuals(ApiTester $I)
    {
        $this->runCompaniesWithIndividualsTests($I, Data::$companiesRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompaniesWithRelationshipIndividuals(ApiTester $I)
    {
        $this->runCompaniesWithIndividualsTests($I, Data::$companiesRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompaniesWithProducts(ApiTester $I)
    {
        $this->runCompaniesWithProductsTests($I, Data::$companiesRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompaniesWithRelationshipCompanyTypes(ApiTester $I)
    {
        $this->runCompaniesWithProductsTests($I, Data::$companiesRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesNoData(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }

    private function addRecords(ApiTester $I): array
    {
        /** @var Companies $comOne */
        $company = $I->addCompanyRecord('com-a');
        $indType = $I->addIndividualTypeRecord('type-a-');
        $indOne  = $I->addIndividualRecord('ind-a-', $company->get('id'), $indType->get('id'));
        $indTwo  = $I->addIndividualRecord('ind-a-', $company->get('id'), $indType->get('id'));
        $prdType = $I->addProductTypeRecord('type-a-');
        $prdOne  = $I->addProductRecord('prd-a-', $prdType->get('id'));
        $prdTwo  = $I->addProductRecord('prd-b-', $prdType->get('id'));
        $I->addCompanyXProduct($company->get('id'), $prdOne->get('id'));
        $I->addCompanyXProduct($company->get('id'), $prdTwo->get('id'));

        return [$company, $prdOne, $prdTwo, $indOne, $indTwo];
    }

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runCompaniesWithAllRelationshipsTests(ApiTester $I, $url)
    {
        list($com, $prdOne, $prdTwo, $indOne, $indTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $com->get('id'),
                Relationships::INDIVIDUALS . ',' . Relationships::PRODUCTS
            )
        );

        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::COMPANIES,
                    'id'            => $com->get('id'),
                    'attributes'    => [
                        'name'    => $com->get('name'),
                        'address' => $com->get('address'),
                        'city'    => $com->get('city'),
                        'phone'   => $com->get('phone'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::COMPANIES,
                            $com->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::INDIVIDUALS => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::COMPANIES,
                                    $com->get('id'),
                                    Relationships::INDIVIDUALS
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::COMPANIES,
                                    $com->get('id'),
                                    Relationships::INDIVIDUALS
                                ),
                            ],
                            'data'  => [
                                [
                                    'type' => Relationships::INDIVIDUALS,
                                    'id'   => $indOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::INDIVIDUALS,
                                    'id'   => $indTwo->get('id'),
                                ],
                            ],
                        ],
                        Relationships::PRODUCTS => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::COMPANIES,
                                    $com->get('id'),
                                    Relationships::PRODUCTS
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::COMPANIES,
                                    $com->get('id'),
                                    Relationships::PRODUCTS
                                ),
                            ],
                            'data'  => [
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => $prdOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => $prdTwo->get('id'),
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::individualResponse($indOne),
                Data::individualResponse($indTwo),
                Data::productResponse($prdOne),
                Data::productResponse($prdTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runCompaniesWithIndividualsTests(ApiTester $I, $url)
    {
        list($com, , , $indOne, $indTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $com->get('id'),
                Relationships::INDIVIDUALS
            )
        );

        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::COMPANIES,
                    'id'            => $com->get('id'),
                    'attributes'    => [
                        'name'    => $com->get('name'),
                        'address' => $com->get('address'),
                        'city'    => $com->get('city'),
                        'phone'   => $com->get('phone'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::COMPANIES,
                            $com->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::INDIVIDUALS => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::COMPANIES,
                                    $com->get('id'),
                                    Relationships::INDIVIDUALS
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::COMPANIES,
                                    $com->get('id'),
                                    Relationships::INDIVIDUALS
                                ),
                            ],
                            'data'  => [
                                [
                                    'type' => Relationships::INDIVIDUALS,
                                    'id'   => $indOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::INDIVIDUALS,
                                    'id'   => $indTwo->get('id'),
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::individualResponse($indOne),
                Data::individualResponse($indTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runCompaniesWithProductsTests(ApiTester $I, $url)
    {
        list($com, $prdOne, $prdTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $com->get('id'),
                Relationships::PRODUCTS
            )
        );

        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::COMPANIES,
                    'id'            => $com->get('id'),
                    'attributes'    => [
                        'name'    => $com->get('name'),
                        'address' => $com->get('address'),
                        'city'    => $com->get('city'),
                        'phone'   => $com->get('phone'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::COMPANIES,
                            $com->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::PRODUCTS => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::COMPANIES,
                                    $com->get('id'),
                                    Relationships::PRODUCTS
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL', 'localhost'),
                                    Relationships::COMPANIES,
                                    $com->get('id'),
                                    Relationships::PRODUCTS
                                ),
                            ],
                            'data'  => [
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => $prdOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => $prdTwo->get('id'),
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::productResponse($prdOne),
                Data::productResponse($prdTwo),
            ]
        );
    }
}

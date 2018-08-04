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
    public function getCompanyUnknownInclude(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $company = $I->addCompanyRecord('com-a-');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesRecordIncludesUrl, $company->get('id'), 'unknown'));
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
    public function getCompaniesSingleSort(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var Companies $comOne */
        $comOne  = $I->addCompanyRecord('com-a-');
        /** @var Companies $comTwo */
        $comTwo  = $I->addCompanyRecord('com-b-');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, 'name'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($comOne),
                Data::companyResponse($comTwo),
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, '-name'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($comTwo),
                Data::companyResponse($comOne),
            ]
        );
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompaniesMultipleSort(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var Companies $comOne */
        $comOne  = $I->addCompanyRecord('com-a-', '', 'city-b');
        /** @var Companies $comTwo */
        $comTwo  = $I->addCompanyRecord('com-b-', '', 'city-b');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, 'city,name'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($comOne),
                Data::companyResponse($comTwo),
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, 'city,-name'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($comTwo),
                Data::companyResponse($comOne),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesInvalidSort(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var Companies $comOne */
        $I->addCompanyRecord('com-a-', '', 'city-b');
        /** @var Companies $comTwo */
        $I->addCompanyRecord('com-b-', '', 'city-b');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, 'unknown'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs400();
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesWithIncludesAllIncludes(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::INDIVIDUALS, Relationships::PRODUCTS]);
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesWithIncludesIndividuals(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::INDIVIDUALS]);
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesWithIncludesProducts(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::PRODUCTS]);
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

    private function checkIncludes(ApiTester $I, array $includes = [])
    {
        list($com, $prdOne, $prdTwo, $indOne, $indTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                Data::$companiesRecordIncludesUrl,
                $com->get('id'),
                implode(',', $includes)
            )
        );

        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();

        $element = [
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
        ];

        $included = [];
        foreach ($includes as $include) {
            if (Relationships::INDIVIDUALS === $include) {
                $element['relationships'][Relationships::INDIVIDUALS] = [
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
                ];

                $included[] = Data::individualResponse($indOne);
                $included[] = Data::individualResponse($indTwo);

            }

            if (Relationships::PRODUCTS === $include) {
                $element['relationships'][Relationships::PRODUCTS] = [
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
                ];

                $included[] = Data::productResponse($prdOne);
                $included[] = Data::productResponse($prdTwo);
            }
        }

        $I->seeSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $I->seeSuccessJsonResponse('included', $included);
        }
    }
}

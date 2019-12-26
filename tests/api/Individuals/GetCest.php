<?php

namespace Phalcon\Api\Tests\api\Individuals;

use ApiTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Page\Data;
use function Phalcon\Api\Core\envValue;

class GetCest
{
    /**
     * @param ApiTester $I
     *
     * @throws ModelException
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
     * @throws ModelException
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
     */
    public function getIndividualsWithIncludesAllIncludes(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::COMPANIES, Relationships::INDIVIDUAL_TYPES]);
    }

    /**
     * @param ApiTester $I
     */
    public function getIndividualsWithIncludesCompanies(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::COMPANIES]);
    }

    /**
     * @param ApiTester $I
     */
    public function getIndividualsWithRelationshipIndividualTypes(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::INDIVIDUAL_TYPES]);
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

    private function checkIncludes(ApiTester $I, array $includes = [])
    {
        list($individual, $individualType, $company) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                Data::$individualsRecordIncludesUrl,
                $individual->get('id'),
                implode(',', $includes)
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();

        $element = [
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
        ];

        $included = [];
        foreach ($includes as $include) {
            if (Relationships::COMPANIES === $include) {
                $element['relationships'][Relationships::COMPANIES] = [
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
                ];

                $included[] = Data::companiesResponse($company);

            }

            if (Relationships::INDIVIDUAL_TYPES === $include) {
                $element['relationships'][Relationships::INDIVIDUAL_TYPES] = [
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
                ];

                $included[] = Data::individualTypeResponse($individualType);
            }
        }

        $I->seeSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $I->seeSuccessJsonResponse('included', $included);
        }
    }
}

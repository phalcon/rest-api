<?php

namespace Phalcon\Api\Tests\api\IndividualTypes;

use ApiTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
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
    public function getIndividualTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $typeOne = $I->addIndividualTypeRecord('type-a-');
        $typeTwo = $I->addIndividualTypeRecord('type-b-');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$individualTypesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::individualTypeResponse($typeOne),
                Data::individualTypeResponse($typeTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getUnknownIndividualTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$individualTypesRecordUrl, 1));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    /**
     * @param ApiTester $I
     *
     * @throws ModelException
     */
    public function getIndividualTypesWithIncludesIndividuals(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var  $company */
        $company        = $I->addCompanyRecord('com-a');
        /** @var IndividualTypes $individualType */
        $individualType = $I->addIndividualTypeRecord('type-a-');
        /** @var Individuals $individualOne */
        $individualOne  = $I->addIndividualRecord('prd-a-', $company->get('id'), $individualType->get('id'));
        /** @var Individuals $individualTwo */
        $individualTwo  = $I->addIndividualRecord('prd-b-', $company->get('id'), $individualType->get('id'));
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                Data::$individualTypesRecordIncludesUrl,
                $individualType->get('id'),
                Relationships::INDIVIDUALS
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::INDIVIDUAL_TYPES,
                    'id'            => $individualType->get('id'),
                    'attributes'    => [
                        'name'        => $individualType->get('name'),
                        'description' => $individualType->get('description'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL'),
                            Relationships::INDIVIDUAL_TYPES,
                            $individualType->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::INDIVIDUALS => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL'),
                                    Relationships::INDIVIDUAL_TYPES,
                                    $individualType->get('id'),
                                    Relationships::INDIVIDUALS
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL'),
                                    Relationships::INDIVIDUAL_TYPES,
                                    $individualType->get('id'),
                                    Relationships::INDIVIDUALS
                                ),
                            ],
                            'data'  => [
                                [
                                    'type' => Relationships::INDIVIDUALS,
                                    'id'   => $individualOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::INDIVIDUALS,
                                    'id'   => $individualTwo->get('id'),
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
                Data::individualResponse($individualOne),
                Data::individualResponse($individualTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getIndividualTypesNoData(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$individualTypesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }
}

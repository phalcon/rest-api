<?php

namespace Niden\Tests\api\IndividualTypes;

use ApiTester;
use Niden\Constants\Relationships;
use Niden\Models\IndividualTypes;
use Page\Data;
use function uniqid;

class GetCest
{
    public function getIndividualTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $typeOne = $I->haveRecordWithFields(
            IndividualTypes::class,
            [
                'name' => uniqid('type-a-'),
            ]
        );
        $typeTwo = $I->haveRecordWithFields(
            IndividualTypes::class,
            [
                'name' => uniqid('type-b-'),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$individualTypesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'id'         => $typeOne->get('id'),
                    'type'       => Relationships::INDIVIDUAL_TYPES,
                    'attributes' => [
                        'name'        => $typeOne->get('name'),
                        'description' => $typeOne->get('description'),
                    ],
                ],
                [
                    'id'         => $typeTwo->get('id'),
                    'type'       => Relationships::INDIVIDUAL_TYPES,
                    'attributes' => [
                        'name'        => $typeTwo->get('name'),
                        'description' => $typeTwo->get('description'),
                    ],
                ],
            ]
        );
    }

    public function getUnknownIndividualTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$individualTypesUrl . '/1');
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

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

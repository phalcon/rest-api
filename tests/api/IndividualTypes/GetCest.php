<?php

namespace Niden\Tests\api\IndividualTypes;

use ApiTester;
use Niden\Constants\Resources;
use Niden\Models\IndividualTypes;
use Niden\Models\Users;
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
            [
                [
                    'id'         => $typeOne->get('id'),
                    'type'       => Resources::INDIVIDUAL_TYPES,
                    'attributes' => [
                        'name'        => $typeOne->get('name'),
                        'description' => $typeOne->get('description'),
                    ],
                ],
                [
                    'id'         => $typeTwo->get('id'),
                    'type'       => Resources::INDIVIDUAL_TYPES,
                    'attributes' => [
                        'name'        => $typeTwo->get('name'),
                        'description' => $typeTwo->get('description'),
                    ],
                ],
            ]
        );
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

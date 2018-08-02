<?php

namespace Niden\Tests\api\IndividualTypes;

use ApiTester;
use Niden\Models\IndividualTypes;
use Page\Data;
use function uniqid;

class GetCest
{
    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getIndividualTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $typeOne = $I->haveRecordWithFields(
            IndividualTypes::class,
            [
                'name'        => uniqid('type-a-'),
                'description' => uniqid('desc-a-'),
            ]
        );
        $typeTwo = $I->haveRecordWithFields(
            IndividualTypes::class,
            [
                'name'        => uniqid('type-b-'),
                'description' => uniqid('desc-b-'),
            ]
        );
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

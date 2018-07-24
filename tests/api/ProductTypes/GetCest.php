<?php

namespace Niden\Tests\api\ProductTypes;

use ApiTester;
use Niden\Constants\Relationships;
use Niden\Models\ProductTypes;
use Page\Data;
use function uniqid;

class GetCest
{
    public function getProductTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $typeOne = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name' => uniqid('type-a-'),
            ]
        );
        $typeTwo = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name' => uniqid('type-b-'),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productTypesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $typeOne->get('id'),
                    'type'       => Relationships::PRODUCT_TYPES,
                    'attributes' => [
                        'name'        => $typeOne->get('name'),
                        'description' => $typeOne->get('description'),
                    ],
                ],
                [
                    'id'         => $typeTwo->get('id'),
                    'type'       => Relationships::PRODUCT_TYPES,
                    'attributes' => [
                        'name'        => $typeTwo->get('name'),
                        'description' => $typeTwo->get('description'),
                    ],
                ],
            ]
        );
    }

    public function getUnknownProductTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productTypesUrl . '/1');
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    public function getProductTypesNoData(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productTypesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }
}

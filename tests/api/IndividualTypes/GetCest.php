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
        $this->addRecord($I);
        $token = $I->apiLogin();

        $typeOne = $I->haveRecordWithFields(
            IndividualTypes::class,
            [
                'idt_name' => uniqid('type-a-'),
            ]
        );
        $typeTwo = $I->haveRecordWithFields(
            IndividualTypes::class,
            [
                'idt_name' => uniqid('type-b-'),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(Data::$individualTypesGetUrl, json_encode(['data' => []]));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $typeOne->get('idt_id'),
                    'type'       => Resources::INDIVIDUAL_TYPES,
                    'attributes' => [
                        'name'        => $typeOne->get('idt_name'),
                        'description' => $typeOne->get('idt_description'),
                    ],
                ],
                [
                    'id'         => $typeTwo->get('idt_id'),
                    'type'       => Resources::INDIVIDUAL_TYPES,
                    'attributes' => [
                        'name'        => $typeTwo->get('idt_name'),
                        'description' => $typeTwo->get('idt_description'),
                    ],
                ],
            ]
        );
    }

    public function getIndividualTypesNoData(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(Data::$individualTypesGetUrl, json_encode(['data' => []]));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }

    private function addRecord(ApiTester $I)
    {
        return $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag'    => 1,
                'usr_username'       => 'testuser',
                'usr_password'       => 'testpassword',
                'usr_issuer'         => 'https://niden.net',
                'usr_token_password' => '12345',
                'usr_token_id'       => '110011',
            ]
        );
    }
}

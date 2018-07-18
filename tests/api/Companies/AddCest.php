<?php

namespace Niden\Tests\api\Companies;

use ApiTester;
use Niden\Constants\Resources;
use Niden\Models\Companies;
use Niden\Models\Users;
use Page\Data;
use function uniqid;

class AddCest
{
    public function addNewCompany(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();
        $name  = uniqid('com');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(
            Data::$companiesAddUrl,
            Data::companyAddJson(
                $name,
                '123 Phalcon way',
                'World',
                '555-444-7777'
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();

        $company = $I->getRecordWithFields(
            Companies::class,
            [
                'com_name' => $name,
            ]
        );
        $I->assertNotEquals(false, $company);

        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $company->get('com_id'),
                    'type'       => Resources::COMPANIES,
                    'attributes' => [
                        'name'    => $company->get('com_name'),
                        'address' => $company->get('com_address'),
                        'city'    => $company->get('com_city'),
                        'phone'   => $company->get('com_telephone'),
                    ],
                ],
            ]
        );

        $I->assertNotEquals(false, $company->delete());
    }

    public function addNewCompanyWithExistingName(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();
        $name  = uniqid('com');
        $I->haveRecordWithFields(
            Companies::class,
            [
                'com_name' => $name
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(
            Data::$companiesAddUrl,
            Data::companyAddJson(
                $name,
                '123 Phalcon way',
                'World',
                '555-444-7777'
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeErrorJsonResponse('The company name already exists in the database');
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

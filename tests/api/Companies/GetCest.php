<?php

namespace Niden\Tests\api\Companies;

use ApiTester;
use Niden\Constants\Resources;
use Niden\Models\Companies;
use Niden\Models\Users;
use Page\Data;
use function uniqid;

class GetCest
{
    public function getCompany(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();

        $comOne = $I->haveRecordWithFields(
            Companies::class,
            [
                'com_name'      => uniqid('com-a-'),
                'com_address'   => uniqid(),
                'com_city'      => uniqid(),
                'com_telephone' => uniqid(),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl . '/' . $comOne->get('com_id'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $comOne->get('com_id'),
                    'type'       => Resources::COMPANIES,
                    'attributes' => [
                        'name'    => $comOne->get('com_name'),
                        'address' => $comOne->get('com_address'),
                        'city'    => $comOne->get('com_city'),
                        'phone'   => $comOne->get('com_telephone'),
                    ],
                ],
            ]
        );
    }

    public function getCompanies(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();

        $comOne = $I->haveRecordWithFields(
            Companies::class,
            [
                'com_name'      => uniqid('com-a-'),
                'com_address'   => uniqid(),
                'com_city'      => uniqid(),
                'com_telephone' => uniqid(),
            ]
        );
        $comTwo = $I->haveRecordWithFields(
            Companies::class,
            [
                'com_name'      => uniqid('com-b-'),
                'com_address'   => uniqid(),
                'com_city'      => uniqid(),
                'com_telephone' => uniqid(),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $comOne->get('com_id'),
                    'type'       => Resources::COMPANIES,
                    'attributes' => [
                        'name'    => $comOne->get('com_name'),
                        'address' => $comOne->get('com_address'),
                        'city'    => $comOne->get('com_city'),
                        'phone'   => $comOne->get('com_telephone'),
                    ],
                ],
                [
                    'id'         => $comTwo->get('com_id'),
                    'type'       => Resources::COMPANIES,
                    'attributes' => [
                        'name'    => $comTwo->get('com_name'),
                        'address' => $comTwo->get('com_address'),
                        'city'    => $comTwo->get('com_city'),
                        'phone'   => $comTwo->get('com_telephone'),
                    ],
                ],
            ]
        );
    }

    public function getCompaniesNoData(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
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

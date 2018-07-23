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
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $comOne = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-a-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl . '/' . $comOne->get('id'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $comOne->get('id'),
                    'type'       => Resources::COMPANIES,
                    'attributes' => [
                        'name'    => $comOne->get('name'),
                        'address' => $comOne->get('address'),
                        'city'    => $comOne->get('city'),
                        'phone'   => $comOne->get('phone'),
                    ],
                ],
            ]
        );
    }

    public function getCompanies(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $comOne = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-a-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );
        $comTwo = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-b-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $comOne->get('id'),
                    'type'       => Resources::COMPANIES,
                    'attributes' => [
                        'name'    => $comOne->get('name'),
                        'address' => $comOne->get('address'),
                        'city'    => $comOne->get('city'),
                        'phone'   => $comOne->get('phone'),
                    ],
                ],
                [
                    'id'         => $comTwo->get('id'),
                    'type'       => Resources::COMPANIES,
                    'attributes' => [
                        'name'    => $comTwo->get('name'),
                        'address' => $comTwo->get('address'),
                        'city'    => $comTwo->get('city'),
                        'phone'   => $comTwo->get('phone'),
                    ],
                ],
            ]
        );
    }

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
}

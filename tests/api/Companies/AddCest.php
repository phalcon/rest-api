<?php

namespace Niden\Tests\api\Companies;

use ApiTester;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Page\Data;
use function uniqid;

class AddCest
{
    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function addNewCompany(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();
        $name  = uniqid('com');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(
            Data::$companiesUrl,
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
                'name' => $name,
            ]
        );
        $I->assertNotEquals(false, $company);

        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($company),
            ]
        );

        $I->assertNotEquals(false, $company->delete());
    }

    /**
     * @param ApiTester $I
     */
    public function addNewCompanyWithExistingName(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();
        $name  = uniqid('com');
        $I->haveRecordWithFields(
            Companies::class,
            [
                'name' => $name,
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(
            Data::$companiesUrl,
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
}

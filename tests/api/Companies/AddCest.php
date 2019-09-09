<?php

namespace Phalcon\Api\Tests\api\Companies;

use ApiTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Companies;
use Page\Data;
use function Phalcon\Api\Core\appUrl;
use function uniqid;

class AddCest
{
    /**
     * @param ApiTester $I
     *
     * @throws ModelException
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
        $I->seeResponseIsSuccessful(Response::CREATED);

        $company = $I->getRecordWithFields(
            Companies::class,
            [
                'name' => $name,
            ]
        );
        $I->assertNotEquals(false, $company);

        $I->seeHttpHeader('Location', appUrl(Relationships::COMPANIES, $company->get('id')));
        $I->seeSuccessJsonResponse(
            'data',
            Data::companiesResponse($company)
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

    /**
     * @param ApiTester $I
     */
    public function addNewCompanyWithoutPostingName(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST(
            Data::$companiesUrl,
            Data::companyAddJson(
                '',
                '123 Phalcon way',
                'World',
                '555-444-7777'
            )
        );
        $I->deleteHeader('Authorization');
        $I->deleteHeader('Authorization');
        $I->seeErrorJsonResponse('The company name is required');
    }
}

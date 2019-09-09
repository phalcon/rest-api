<?php

namespace Phalcon\Api\Tests\api\Companies;

use ApiTester;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Page\Data;

class GetSortCest extends GetBase
{
    /**
     * @param ApiTester $I
     *
     * @throws ModelException
     */
    public function getCompaniesSingleSort(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var Companies $comOne */
        $comOne  = $I->addCompanyRecord('com-a-');
        /** @var Companies $comTwo */
        $comTwo  = $I->addCompanyRecord('com-b-');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, 'name'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comOne),
                Data::companiesResponse($comTwo),
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, '-name'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comTwo),
                Data::companiesResponse($comOne),
            ]
        );
    }

    /**
     * @param ApiTester $I
     *
     * @throws ModelException
     */
    public function getCompaniesMultipleSort(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var Companies $comOne */
        $comOne  = $I->addCompanyRecord('com-a-', '', 'city-b');
        /** @var Companies $comTwo */
        $comTwo  = $I->addCompanyRecord('com-b-', '', 'city-b');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, 'city,name'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comOne),
                Data::companiesResponse($comTwo),
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, 'city,-name'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comTwo),
                Data::companiesResponse($comOne),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesInvalidSort(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var Companies $comOne */
        $I->addCompanyRecord('com-a-', '', 'city-b');
        /** @var Companies $comTwo */
        $I->addCompanyRecord('com-b-', '', 'city-b');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesSortUrl, 'unknown'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs400();
    }

}

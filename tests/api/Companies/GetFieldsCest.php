<?php

namespace Phalcon\Api\Tests\api\Companies;

use ApiTester;
use Phalcon\Api\Constants\Relationships;
use Page\Data;
use function Phalcon\Api\Core\envValue;

class GetFieldsCest extends GetBase
{
    public function getCompaniesWithIncludesAndFields(ApiTester $I)
    {
        $this->runTestsCompaniesWithIncludesAndFields($I);
    }

    public function getCompaniesWithIncludesAndUnknownFields(ApiTester $I)
    {
        $this->runTestsCompaniesWithIncludesAndFields($I, ',unknown-product-field');
    }

    private function runTestsCompaniesWithIncludesAndFields(ApiTester $I, string $fields = '')
    {
        list($com, $prdOne, $prdTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                Data::$companiesRecordIncludesUrl,
                $com->get('id'),
                Relationships::PRODUCTS
            ) .
            '&fields['. Relationships::COMPANIES . ']=id,name,city' .
            '&fields['. Relationships::PRODUCTS . ']=id,name,price' . $fields
        );

        $I->deleteHeader('Authorization');

        $I->seeResponseIsSuccessful();

        $included = [];
        $element  = [
            'type'          => Relationships::COMPANIES,
            'id'            => $com->get('id'),
            'attributes'    => [
                'name'    => $com->get('name'),
                'city'    => $com->get('city'),
            ],
            'links'         => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL', 'localhost'),
                    Relationships::COMPANIES,
                    $com->get('id')
                ),
            ],
        ];

        $element['relationships'][Relationships::PRODUCTS] = [
            'links' => [
                'self'    => sprintf(
                    '%s/%s/%s/relationships/%s',
                    envValue('APP_URL', 'localhost'),
                    Relationships::COMPANIES,
                    $com->get('id'),
                    Relationships::PRODUCTS
                ),
                'related' => sprintf(
                    '%s/%s/%s/%s',
                    envValue('APP_URL', 'localhost'),
                    Relationships::COMPANIES,
                    $com->get('id'),
                    Relationships::PRODUCTS
                ),
            ],
            'data'  => [
                [
                    'type' => Relationships::PRODUCTS,
                    'id'   => $prdOne->get('id'),
                ],
                [
                    'type' => Relationships::PRODUCTS,
                    'id'   => $prdTwo->get('id'),
                ],
            ],
        ];

        $included[] = Data::productFieldsResponse($prdOne);
        $included[] = Data::productFieldsResponse($prdTwo);

        $I->seeSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $I->seeSuccessJsonResponse('included', $included);
        }
    }
}

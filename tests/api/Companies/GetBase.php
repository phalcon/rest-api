<?php

namespace Phalcon\Api\Tests\api\Companies;

use ApiTester;
use Phalcon\Api\Models\Companies;

class GetBase
{
    protected function addRecords(ApiTester $I): array
    {
        /** @var Companies $comOne */
        $company = $I->addCompanyRecord('com-a');
        $indType = $I->addIndividualTypeRecord('type-a-');
        $indOne  = $I->addIndividualRecord('ind-a-', $company->get('id'), $indType->get('id'));
        $indTwo  = $I->addIndividualRecord('ind-a-', $company->get('id'), $indType->get('id'));
        $prdType = $I->addProductTypeRecord('type-a-');
        $prdOne  = $I->addProductRecord('prd-a-', $prdType->get('id'));
        $prdTwo  = $I->addProductRecord('prd-b-', $prdType->get('id'));
        $I->addCompanyXProduct($company->get('id'), $prdOne->get('id'));
        $I->addCompanyXProduct($company->get('id'), $prdTwo->get('id'));

        return [$company, $prdOne, $prdTwo, $indOne, $indTwo];
    }
}

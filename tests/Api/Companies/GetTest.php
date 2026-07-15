<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Tests\Api\Companies;

use Phalcon\Api\Tests\Support\Data;

use function sprintf;

final class GetTest extends AbstractGetTestCase
{
    public function testGetCompanies(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $comOne = $this->addCompanyRecord('com-a-');
        $comTwo = $this->addCompanyRecord('com-b-');

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$companiesUrl);
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comOne),
                Data::companiesResponse($comTwo),
            ]
        );
    }

    public function testGetCompaniesNoData(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$companiesUrl);
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse();
    }

    public function testGetCompany(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $company = $this->addCompanyRecord('com-a-');

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$companiesRecordUrl, $company->get('id')));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($company),
            ]
        );
    }

    public function testGetUnknownCompany(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$companiesRecordUrl, 1));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIs404();
    }
}

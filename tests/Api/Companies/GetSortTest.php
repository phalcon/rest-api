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

final class GetSortTest extends AbstractGetTestCase
{
    public function testGetCompaniesInvalidSort(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->addCompanyRecord('com-a-', '', 'city-b');
        $this->addCompanyRecord('com-b-', '', 'city-b');

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$companiesSortUrl, 'unknown'));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIs400();
    }

    public function testGetCompaniesMultipleSort(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $comOne = $this->addCompanyRecord('com-a-', '', 'city-b');
        $comTwo = $this->addCompanyRecord('com-b-', '', 'city-b');

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$companiesSortUrl, 'city,name'));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comOne),
                Data::companiesResponse($comTwo),
            ]
        );

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$companiesSortUrl, 'city,-name'));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comTwo),
                Data::companiesResponse($comOne),
            ]
        );
    }

    public function testGetCompaniesSingleSort(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $comOne = $this->addCompanyRecord('com-a-');
        $comTwo = $this->addCompanyRecord('com-b-');

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$companiesSortUrl, 'name'));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comOne),
                Data::companiesResponse($comTwo),
            ]
        );

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$companiesSortUrl, '-name'));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($comTwo),
                Data::companiesResponse($comOne),
            ]
        );
    }
}

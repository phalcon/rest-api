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

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Tests\Api\AbstractApiTestCase;
use Phalcon\Api\Tests\Support\Data;

use function Phalcon\Api\Core\appUrl;
use function uniqid;

final class AddTest extends AbstractApiTestCase
{
    public function testAddNewCompany(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();
        $name  = uniqid('com');

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendPost(
            Data::$companiesUrl,
            Data::companyAddJson(
                $name,
                '123 Phalcon way',
                'World',
                '555-444-7777'
            )
        );
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful(Response::CREATED);

        $company = $this->getRecordWithFields(
            Companies::class,
            [
                'name' => $name,
            ]
        );
        $this->assertNotEquals(false, $company);

        $this->assertHttpHeader('Location', appUrl(Relationships::COMPANIES, $company->get('id')));
        $this->assertSuccessJsonResponse(
            'data',
            Data::companiesAddResponse($company)
        );

        $this->assertNotEquals(false, $company->delete());
    }

    public function testAddNewCompanyWithExistingName(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();
        $name  = uniqid('com');

        $this->haveRecordWithFields(
            Companies::class,
            [
                'name' => $name,
            ]
        );

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendPost(
            Data::$companiesUrl,
            Data::companyAddJson(
                $name,
                '123 Phalcon way',
                'World',
                '555-444-7777'
            )
        );
        $this->unsetHttpHeader('Authorization');
        $this->assertErrorJsonResponse('The company name already exists in the database');
    }

    public function testAddNewCompanyWithoutPostingName(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendPost(
            Data::$companiesUrl,
            Data::companyAddJson(
                '',
                '123 Phalcon way',
                'World',
                '555-444-7777'
            )
        );
        $this->unsetHttpHeader('Authorization');
        $this->assertErrorJsonResponse('The company name is required');
    }
}

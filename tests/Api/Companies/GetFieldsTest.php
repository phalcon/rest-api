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
use Phalcon\Api\Tests\Support\Data;

use function count;
use function sprintf;

final class GetFieldsTest extends AbstractGetTestCase
{
    public function testGetCompaniesWithIncludesAndFields(): void
    {
        $this->runCompaniesWithIncludesAndFields();
    }

    public function testGetCompaniesWithIncludesAndUnknownFields(): void
    {
        $this->runCompaniesWithIncludesAndFields(',unknown-product-field');
    }

    private function runCompaniesWithIncludesAndFields(string $fields = ''): void
    {
        [$com, $prdOne, $prdTwo] = $this->addRecords();

        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->sendGetAs(
            $token,
            sprintf(
                Data::$companiesRecordIncludesUrl,
                $com->get('id'),
                Relationships::PRODUCTS
            ) .
            '&fields[' . Relationships::COMPANIES . ']=id,name,city' .
            '&fields[' . Relationships::PRODUCTS . ']=id,name,price' . $fields
        );
        $this->assertResponseIsSuccessful();

        $element = Data::resource(
            Relationships::COMPANIES,
            $com,
            [
                'name' => $com->get('name'),
                'city' => $com->get('city'),
            ]
        );

        $element['relationships'][Relationships::PRODUCTS] = [
            'links' => Data::relationshipLinks(
                Relationships::COMPANIES,
                $com->get('id'),
                Relationships::PRODUCTS
            ),
            'data'  => [
                [
                    'type' => Relationships::PRODUCTS,
                    'id'   => (string) $prdOne->get('id'),
                ],
                [
                    'type' => Relationships::PRODUCTS,
                    'id'   => (string) $prdTwo->get('id'),
                ],
            ],
        ];

        $included   = [];
        $included[] = Data::productFieldsResponse($prdOne);
        $included[] = Data::productFieldsResponse($prdTwo);

        $this->assertSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $this->assertSuccessJsonResponse('included', $included);
        }
    }
}

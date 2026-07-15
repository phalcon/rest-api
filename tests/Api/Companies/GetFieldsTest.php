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
use function Phalcon\Api\Core\envValue;
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

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(
            sprintf(
                Data::$companiesRecordIncludesUrl,
                $com->get('id'),
                Relationships::PRODUCTS
            ) .
            '&fields[' . Relationships::COMPANIES . ']=id,name,city' .
            '&fields[' . Relationships::PRODUCTS . ']=id,name,price' . $fields
        );
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();

        $element = [
            'type'       => Relationships::COMPANIES,
            'id'         => $com->get('id'),
            'attributes' => [
                'name' => $com->get('name'),
                'city' => $com->get('city'),
            ],
            'links'      => [
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

        $included   = [];
        $included[] = Data::productFieldsResponse($prdOne);
        $included[] = Data::productFieldsResponse($prdTwo);

        $this->assertSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $this->assertSuccessJsonResponse('included', $included);
        }
    }
}

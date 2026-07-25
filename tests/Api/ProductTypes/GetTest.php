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

namespace Phalcon\Api\Tests\Api\ProductTypes;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Tests\Api\AbstractApiTestCase;
use Phalcon\Api\Tests\Support\Data;

use function sprintf;

final class GetTest extends AbstractApiTestCase
{
    public function testGetProductTypes(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $typeOne = $this->addProductTypeRecord('type-a-');
        $typeTwo = $this->addProductTypeRecord('type-b-');

        $this->sendGetAs($token, Data::$productTypesUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::productTypeResponse($typeOne),
                Data::productTypeResponse($typeTwo),
            ]
        );
    }

    public function testGetProductTypesNoData(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->sendGetAs($token, Data::$productTypesUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse();
    }

    public function testGetProductTypesWithIncludesProducts(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $productType = $this->addProductTypeRecord('type-a-');
        $productOne  = $this->addProductRecord('prd-a-', $productType->get('id'));
        $productTwo  = $this->addProductRecord('prd-b-', $productType->get('id'));

        $this->sendGetAs(
            $token,
            sprintf(
                Data::$productTypesRecordIncludesUrl,
                $productType->get('id'),
                Relationships::PRODUCTS
            )
        );
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::productTypeResponse($productType) + [
                    'relationships' => [
                        Relationships::PRODUCTS => [
                            'links' => Data::relationshipLinks(
                                Relationships::PRODUCT_TYPES,
                                $productType->get('id'),
                                Relationships::PRODUCTS
                            ),
                            'data'  => [
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => (string) $productOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => (string) $productTwo->get('id'),
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->assertSuccessJsonResponse(
            'included',
            [
                Data::productResponse($productOne),
                Data::productResponse($productTwo),
            ]
        );
    }

    public function testGetUnknownProductTypes(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->sendGetAs($token, sprintf(Data::$productTypesRecordUrl, 1));
        $this->assertResponseIs404();
    }
}

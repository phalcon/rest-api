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

use function Phalcon\Api\Core\envValue;
use function sprintf;

final class GetTest extends AbstractApiTestCase
{
    public function testGetProductTypes(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $typeOne = $this->addProductTypeRecord('type-a-');
        $typeTwo = $this->addProductTypeRecord('type-b-');

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$productTypesUrl);
        $this->unsetHttpHeader('Authorization');
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

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$productTypesUrl);
        $this->unsetHttpHeader('Authorization');
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

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(
            sprintf(
                Data::$productTypesRecordIncludesUrl,
                $productType->get('id'),
                Relationships::PRODUCTS
            )
        );
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::PRODUCT_TYPES,
                    'id'            => (string) $productType->get('id'),
                    'attributes'    => [
                        'name'        => $productType->get('name'),
                        'description' => $productType->get('description'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL'),
                            Relationships::PRODUCT_TYPES,
                            $productType->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::PRODUCTS => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL'),
                                    Relationships::PRODUCT_TYPES,
                                    $productType->get('id'),
                                    Relationships::PRODUCTS
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL'),
                                    Relationships::PRODUCT_TYPES,
                                    $productType->get('id'),
                                    Relationships::PRODUCTS
                                ),
                            ],
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

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$productTypesRecordUrl, 1));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIs404();
    }
}

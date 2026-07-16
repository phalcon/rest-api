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

namespace Phalcon\Api\Tests\Api\Products;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Tests\Api\AbstractApiTestCase;
use Phalcon\Api\Tests\Support\Data;

use function count;
use function implode;
use function Phalcon\Api\Core\envValue;
use function sprintf;

final class GetTest extends AbstractApiTestCase
{
    public function testGetProduct(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $productType = $this->addProductTypeRecord('prt-a-');
        $product     = $this->addProductRecord('prd-a-', $productType->get('id'));

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$productsRecordUrl, $product->get('id')));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::productResponse($product),
            ]
        );
    }

    public function testGetProducts(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $productType = $this->addProductTypeRecord('prt-a-');
        $productOne  = $this->addProductRecord('prd-a-', $productType->get('id'));
        $productTwo  = $this->addProductRecord('prd-b-', $productType->get('id'));

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$productsUrl);
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::productResponse($productOne),
                Data::productResponse($productTwo),
            ]
        );
    }

    public function testGetProductsNoData(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$productsUrl);
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse();
    }

    public function testGetProductsWithIncludesAllIncludes(): void
    {
        $this->checkIncludes([Relationships::COMPANIES, Relationships::PRODUCT_TYPES]);
    }

    public function testGetProductsWithIncludesCompanies(): void
    {
        $this->checkIncludes([Relationships::COMPANIES]);
    }

    public function testGetProductsWithIncludesProductTypes(): void
    {
        $this->checkIncludes([Relationships::PRODUCT_TYPES]);
    }

    public function testGetUnknownProduct(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$productsRecordUrl, 1));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIs404();
    }

    /**
     * Returns [$product, $productType, $comOne, $comTwo].
     *
     * @return array<int, mixed>
     */
    private function addRecords(): array
    {
        $comOne      = $this->addCompanyRecord('com-a');
        $comTwo      = $this->addCompanyRecord('com-b');
        $productType = $this->addProductTypeRecord('prt-a-');
        $product     = $this->addProductRecord('prd-a-', $productType->get('id'));

        $this->addCompanyXProduct($comOne->get('id'), $product->get('id'));
        $this->addCompanyXProduct($comTwo->get('id'), $product->get('id'));

        return [$product, $productType, $comOne, $comTwo];
    }

    /**
     * @param array<int, string> $includes
     */
    private function checkIncludes(array $includes = []): void
    {
        [$product, $productType, $comOne, $comTwo] = $this->addRecords();

        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(
            sprintf(
                Data::$productsRecordIncludesUrl,
                $product->get('id'),
                implode(',', $includes)
            )
        );
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();

        $element = [
            'type'       => Relationships::PRODUCTS,
            'id'         => (string) $product->get('id'),
            'attributes' => [
                'typeId'      => $productType->get('id'),
                'name'        => $product->get('name'),
                'description' => $product->get('description'),
                'quantity'    => $product->get('quantity'),
                'price'       => $product->get('price'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL', 'localhost'),
                    Relationships::PRODUCTS,
                    $product->get('id')
                ),
            ],
        ];

        $included = [];
        foreach ($includes as $include) {
            if (Relationships::COMPANIES === $include) {
                $element['relationships'][Relationships::COMPANIES] = [
                    'links' => [
                        'self'    => sprintf(
                            '%s/%s/%s/relationships/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::PRODUCTS,
                            $product->get('id'),
                            Relationships::COMPANIES
                        ),
                        'related' => sprintf(
                            '%s/%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::PRODUCTS,
                            $product->get('id'),
                            Relationships::COMPANIES
                        ),
                    ],
                    'data'  => [
                        [
                            'type' => Relationships::COMPANIES,
                            'id'   => (string) $comOne->get('id'),
                        ],
                        [
                            'type' => Relationships::COMPANIES,
                            'id'   => (string) $comTwo->get('id'),
                        ],
                    ],
                ];

                $included[] = Data::companiesResponse($comOne);
                $included[] = Data::companiesResponse($comTwo);
            }

            if (Relationships::PRODUCT_TYPES === $include) {
                $element['relationships'][Relationships::PRODUCT_TYPES] = [
                    'links' => [
                        'self'    => sprintf(
                            '%s/%s/%s/relationships/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::PRODUCTS,
                            $product->get('id'),
                            Relationships::PRODUCT_TYPES
                        ),
                        'related' => sprintf(
                            '%s/%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::PRODUCTS,
                            $product->get('id'),
                            Relationships::PRODUCT_TYPES
                        ),
                    ],
                    'data'  => [
                        'type' => Relationships::PRODUCT_TYPES,
                        'id'   => (string) $productType->get('id'),
                    ],
                ];

                $included[] = Data::productTypeResponse($productType);
            }
        }

        $this->assertSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $this->assertSuccessJsonResponse('included', $included);
        }
    }
}

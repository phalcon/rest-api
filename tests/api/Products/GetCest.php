<?php

namespace Niden\Tests\api\Products;

use ApiTester;
use Niden\Constants\Relationships;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Page\Data;
use function Niden\Core\envValue;
use function uniqid;

class GetCest
{
    public function getProduct(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var ProductTypes $productType */
        $productType = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name'        => uniqid('prt-a-'),
                'description' => uniqid(),
            ]
        );

        /** @var Products $product */
        $product = $I->haveRecordWithFields(
            Products::class,
            [
                'name'        => uniqid('prd-a-'),
                'typeId'      => $productType->get('id'),
                'description' => uniqid(),
                'quantity'    => 25,
                'price'       => 19.99,
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productsUrl . '/' . $product->get('id'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $product->get('id'),
                    'type'       => Relationships::PRODUCTS,
                    'attributes' => [
                        'name'        => $product->get('name'),
                        'typeId'      => $productType->get('id'),
                        'description' => $product->get('description'),
                        'quantity'    => $product->get('quantity'),
                        'price'       => $product->get('price'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/products/%s',
                            envValue('APP_URL', 'localhost'),
                            $product->get('id')
                        ),
                    ],
                ],
            ]
        );
    }

    public function getUnknownProduct(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productsUrl . '/1');
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    public function getProducts(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var ProductTypes $productType */
        $productType = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name'        => uniqid('prt-a-'),
                'description' => uniqid(),
            ]
        );

        /** @var Products $productOne */
        $productOne = $I->haveRecordWithFields(
            Products::class,
            [
                'name'        => uniqid('prd-a-'),
                'typeId'      => $productType->get('id'),
                'description' => uniqid(),
                'quantity'    => 25,
                'price'       => 19.99,
            ]
        );

        /** @var Products $productTwo */
        $productTwo = $I->haveRecordWithFields(
            Products::class,
            [
                'name'        => uniqid('prd-b-'),
                'typeId'      => $productType->get('id'),
                'description' => uniqid(),
                'quantity'    => 25,
                'price'       => 19.99,
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productsUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $productOne->get('id'),
                    'type'       => Relationships::PRODUCTS,
                    'attributes' => [
                        'name'        => $productOne->get('name'),
                        'typeId'      => $productType->get('id'),
                        'description' => $productOne->get('description'),
                        'quantity'    => $productOne->get('quantity'),
                        'price'       => $productOne->get('price'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/products/%s',
                            envValue('APP_URL', 'localhost'),
                            $productOne->get('id')
                        ),
                    ],
                ],
                [
                    'id'         => $productTwo->get('id'),
                    'type'       => Relationships::PRODUCTS,
                    'attributes' => [
                        'name'        => $productTwo->get('name'),
                        'typeId'      => $productType->get('id'),
                        'description' => $productTwo->get('description'),
                        'quantity'    => $productTwo->get('quantity'),
                        'price'       => $productTwo->get('price'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/products/%s',
                            envValue('APP_URL', 'localhost'),
                            $productTwo->get('id')
                        ),
                    ],
                ],
            ]
        );
    }

    public function getProductsNoData(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productsUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }
}

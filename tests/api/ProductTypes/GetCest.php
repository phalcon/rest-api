<?php

namespace Niden\Tests\api\ProductTypes;

use ApiTester;
use Niden\Constants\Relationships;
use function Niden\Core\envValue;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Page\Data;
use function uniqid;

class GetCest
{
    public function getProductTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $typeOne = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name' => uniqid('type-a-'),
            ]
        );
        $typeTwo = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name' => uniqid('type-b-'),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productTypesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'id'         => $typeOne->get('id'),
                    'type'       => Relationships::PRODUCT_TYPES,
                    'attributes' => [
                        'name'        => $typeOne->get('name'),
                        'description' => $typeOne->get('description'),
                    ],
                ],
                [
                    'id'         => $typeTwo->get('id'),
                    'type'       => Relationships::PRODUCT_TYPES,
                    'attributes' => [
                        'name'        => $typeTwo->get('name'),
                        'description' => $typeTwo->get('description'),
                    ],
                ],
            ]
        );
    }

    public function getUnknownProductTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productTypesUrl . '/1');
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    public function getProductTypesWithProducts(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $productType = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name'        => uniqid('type-a-'),
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
        $I->sendGET(Data::$productTypesUrl . '/' . $productType->get('id') . '/relationships/' . Relationships::PRODUCTS);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();

        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'       => Relationships::PRODUCT_TYPES,
                    'id'         => $productType->get('id'),
                    'attributes' => [
                        'name'        => $productType->get('name'),
                        'description' => $productType->get('description'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL'),
                            Relationships::PRODUCT_TYPES,
                            $productType->get('id')
                        )
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
                            'data' => [
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => $productOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => $productTwo->get('id'),
                                ],
                            ]
                        ]
                    ]
                ],
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                [
                    'type'       => Relationships::PRODUCTS,
                    'id'         => $productOne->get('id'),
                    'attributes' => [
                        'typeId'      => $productOne->get('typeId'),
                        'name'        => $productOne->get('name'),
                        'description' => $productOne->get('description'),
                        'quantity'    => $productOne->get('quantity'),
                        'price'       => $productOne->get('price'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL'),
                            Relationships::PRODUCTS,
                            $productOne->get('id')
                        ),
                    ],
                ],
                [
                    'type'       => Relationships::PRODUCTS,
                    'id'         => $productTwo->get('id'),
                    'attributes' => [
                        'typeId'      => $productTwo->get('typeId'),
                        'name'        => $productTwo->get('name'),
                        'description' => $productTwo->get('description'),
                        'quantity'    => $productTwo->get('quantity'),
                        'price'       => $productTwo->get('price'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL'),
                            Relationships::PRODUCTS,
                            $productTwo->get('id')
                        ),
                    ],
                ],
            ]
        );
    }

    public function getProductTypesNoData(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productTypesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }
}

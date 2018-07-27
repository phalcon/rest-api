<?php

namespace Niden\Tests\api\Companies;

use ApiTester;
use Niden\Constants\Relationships;
use function Niden\Core\envValue;
use Niden\Models\Companies;
use Niden\Models\CompaniesXProducts;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Page\Data;
use function uniqid;

class GetCest
{
    public function getCompany(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $comOne = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-a-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl . '/' . $comOne->get('id'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'id'         => $comOne->get('id'),
                    'type'       => Relationships::COMPANIES,
                    'attributes' => [
                        'name'    => $comOne->get('name'),
                        'address' => $comOne->get('address'),
                        'city'    => $comOne->get('city'),
                        'phone'   => $comOne->get('phone'),
                    ],
                ],
            ]
        );
    }

    public function getUnknownCompany(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl . '/9999');
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    public function getCompanies(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $comOne = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-a-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );
        $comTwo = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-b-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'id'         => $comOne->get('id'),
                    'type'       => Relationships::COMPANIES,
                    'attributes' => [
                        'name'    => $comOne->get('name'),
                        'address' => $comOne->get('address'),
                        'city'    => $comOne->get('city'),
                        'phone'   => $comOne->get('phone'),
                    ],
                ],
                [
                    'id'         => $comTwo->get('id'),
                    'type'       => Relationships::COMPANIES,
                    'attributes' => [
                        'name'    => $comTwo->get('name'),
                        'address' => $comTwo->get('address'),
                        'city'    => $comTwo->get('city'),
                        'phone'   => $comTwo->get('phone'),
                    ],
                ],
            ]
        );
    }

    public function getCompaniesWithProducts(ApiTester $I)
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

        $comOne = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-a-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );

        $I->haveRecordWithFields(
            CompaniesXProducts::class,
            [
                'companyId' => $comOne->get('id'),
                'productId' => $productOne->get('id'),
            ]
        );

        $I->haveRecordWithFields(
            CompaniesXProducts::class,
            [
                'companyId' => $comOne->get('id'),
                'productId' => $productTwo->get('id'),
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl . '/' . $comOne->get('id') . '/relationships/' . Relationships::PRODUCTS);
        $I->deleteHeader('Authorization');

        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'id'         => $comOne->get('id'),
                    'type'       => Relationships::COMPANIES,
                    'attributes' => [
                        'name'    => $comOne->get('name'),
                        'address' => $comOne->get('address'),
                        'city'    => $comOne->get('city'),
                        'phone'   => $comOne->get('phone'),
                    ],
                    'links'      => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL'),
                            Relationships::COMPANIES,
                            $comOne->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::PRODUCTS => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL'),
                                    Relationships::COMPANIES,
                                    $comOne->get('id'),
                                    Relationships::PRODUCTS

                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL'),
                                    Relationships::COMPANIES,
                                    $comOne->get('id'),
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
                ]
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

    public function getCompaniesWithUnknownRelationship(ApiTester $I)
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

        $comOne = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid('com-a-'),
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );

        $I->haveRecordWithFields(
            CompaniesXProducts::class,
            [
                'companyId' => $comOne->get('id'),
                'productId' => $productOne->get('id'),
            ]
        );

        $I->haveRecordWithFields(
            CompaniesXProducts::class,
            [
                'companyId' => $comOne->get('id'),
                'productId' => $productTwo->get('id'),
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl . '/' . $comOne->get('id') . '/relationships/unknown');
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    public function getCompaniesNoData(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }
}

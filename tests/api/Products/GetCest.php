<?php

namespace Niden\Tests\api\Products;

use ApiTester;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Page\Data;
use function Niden\Core\envValue;

class GetCest
{
    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProduct(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var ProductTypes $productType */
        $productType = $I->addProductTypeRecord('prt-a-');
        /** @var Products $product */
        $product     = $I->addProductRecord('prd-a-', $productType->get('id'));
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$productsRecordUrl, $product->get('id')));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::productResponse($product),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getUnknownProduct(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$productsRecordUrl, 1));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProducts(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var ProductTypes $productType */
        $productType = $I->addProductTypeRecord('prt-a-');
        /** @var Products $productOne */
        $productOne  = $I->addProductRecord('prd-a-', $productType->get('id'));
        /** @var Products $productTwo */
        $productTwo  = $I->addProductRecord('prd-b-', $productType->get('id'));

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productsUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::productResponse($productOne),
                Data::productResponse($productTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductsWithAllRelationships(ApiTester $I)
    {
        $this->runProductsWithAllRelationshipsTests($I, Data::$productsRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductsWithRelationshipAllRelationships(ApiTester $I)
    {
        $this->runProductsWithAllRelationshipsTests($I, Data::$productsRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductsWithCompanies(ApiTester $I)
    {
        $this->runProductsWithCompaniesTests($I, Data::$productsRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductsWithRelationshipCompanies(ApiTester $I)
    {
        $this->runProductsWithCompaniesTests($I, Data::$productsRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductsWithProductTypes(ApiTester $I)
    {
        $this->runProductsWithProductTypesTests($I, Data::$productsRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductsWithRelationshipProductTypes(ApiTester $I)
    {
        $this->runProductsWithProductTypesTests($I, Data::$productsRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     */
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

    private function addRecords(ApiTester $I): array
    {
        /** @var Companies $comOne */
        $comOne      = $I->addCompanyRecord('com-a');
        /** @var Companies $comTwo */
        $comTwo      = $I->addCompanyRecord('com-b');
        /** @var ProductTypes $productType */
        $productType = $I->addProductTypeRecord('prt-a-');
        /** @var Products $product */
        $product     = $I->addProductRecord('prd-a-', $productType->get('id'));
        $I->addCompanyXProduct($comOne->get('id'), $product->get('id'));
        $I->addCompanyXProduct($comTwo->get('id'), $product->get('id'));

        return [$product, $productType, $comOne, $comTwo];
    }

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runProductsWithAllRelationshipsTests(ApiTester $I, $url)
    {
        list($product, $productType, $comOne, $comTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $product->get('id'),
                Relationships::COMPANIES . ',' . Relationships::PRODUCT_TYPES
            )
        );

        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::PRODUCTS,
                    'id'            => $product->get('id'),
                    'attributes'    => [
                        'typeId'      => $productType->get('id'),
                        'name'        => $product->get('name'),
                        'description' => $product->get('description'),
                        'quantity'    => $product->get('quantity'),
                        'price'       => $product->get('price'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::PRODUCTS,
                            $product->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::COMPANIES => [
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
                                    'id'   => $comOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::COMPANIES,
                                    'id'   => $comTwo->get('id'),
                                ],
                            ],
                        ],
                        Relationships::PRODUCT_TYPES => [
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
                                'id'   => $productType->get('id'),
                            ],
                        ],
                    ],
                ],
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::companyResponse($comOne),
                Data::companyResponse($comTwo),
                Data::productTypeResponse($productType),
            ]
        );
    }

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runProductsWithCompaniesTests(ApiTester $I, $url)
    {
        list($product, $productType, $comOne, $comTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $product->get('id'),
                Relationships::COMPANIES
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::PRODUCTS,
                    'id'            => $product->get('id'),
                    'attributes'    => [
                        'typeId'      => $productType->get('id'),
                        'name'        => $product->get('name'),
                        'description' => $product->get('description'),
                        'quantity'    => $product->get('quantity'),
                        'price'       => $product->get('price'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::PRODUCTS,
                            $product->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::COMPANIES => [
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
                                    'id'   => $comOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::COMPANIES,
                                    'id'   => $comTwo->get('id'),
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::companyResponse($comOne),
                Data::companyResponse($comTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runProductsWithProductTypesTests(ApiTester $I, $url)
    {
        list($product, $productType) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $product->get('id'),
                Relationships::PRODUCT_TYPES
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::PRODUCTS,
                    'id'            => $product->get('id'),
                    'attributes'    => [
                        'typeId'      => $productType->get('id'),
                        'name'        => $product->get('name'),
                        'description' => $product->get('description'),
                        'quantity'    => $product->get('quantity'),
                        'price'       => $product->get('price'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::PRODUCTS,
                            $product->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::PRODUCT_TYPES => [
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
                                'id'   => $productType->get('id'),
                            ],
                        ],
                    ],
                ],
            ]
        );

        $I->seeSuccessJsonResponse(
            'included',
            [
                Data::productTypeResponse($productType),
            ]
        );
    }
}

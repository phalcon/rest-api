<?php

namespace Niden\Tests\api\ProductTypes;

use ApiTester;
use Niden\Constants\Relationships;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Page\Data;
use function Niden\Core\envValue;
use function uniqid;

class GetCest
{
    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $typeOne = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name'        => uniqid('type-a-'),
                'description' => uniqid('desc-a-'),
            ]
        );
        $typeTwo = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'name'        => uniqid('type-b-'),
                'description' => uniqid('desc-b-'),
            ]
        );
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productTypesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::productTypeResponse($typeOne),
                Data::productTypeResponse($typeTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getUnknownProductTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$productTypesRecordUrl, 1));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductTypesWithRelationshipProducts(ApiTester $I)
    {
        $this->runProductTypesWithProductsTests($I, Data::$productTypesRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getProductTypesWithProducts(ApiTester $I)
    {
        $this->runProductTypesWithProductsTests($I, Data::$productTypesRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     */
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

    /**
     * @param ApiTester $I
     * @param           $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runProductTypesWithProductsTests(ApiTester $I, $url)
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
        $I->sendGET(
            sprintf(
                $url,
                $productType->get('id'),
                Relationships::PRODUCTS
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();

        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::PRODUCT_TYPES,
                    'id'            => $productType->get('id'),
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
                                    'id'   => $productOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::PRODUCTS,
                                    'id'   => $productTwo->get('id'),
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
                Data::productResponse($productOne),
                Data::productResponse($productTwo),
            ]
        );
    }
}

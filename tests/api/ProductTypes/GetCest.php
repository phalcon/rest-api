<?php

namespace Phalcon\Api\Tests\api\ProductTypes;

use ApiTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Page\Data;
use function Phalcon\Api\Core\envValue;

class GetCest
{
    /**
     * @param ApiTester $I
     * @throws ModelException
     */
    public function getProductTypes(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $typeOne = $I->addProductTypeRecord('type-a-');
        $typeTwo = $I->addProductTypeRecord('type-b-');
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
     * @throws ModelException
     */
    public function getProductTypesWithIncludesProducts(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var ProductTypes $productType */
        $productType = $I->addProductTypeRecord('type-a-');
        /** @var Products $productOne */
        $productOne = $I->addProductRecord('prd-a-', $productType->get('id'));
        /** @var Products $productTwo */
        $productTwo = $I->addProductRecord('prd-b-', $productType->get('id'));
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                Data::$productTypesRecordIncludesUrl,
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
}

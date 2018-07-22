<?php

namespace Niden\Tests\api\Products;

use ApiTester;
use Niden\Constants\Resources;
use Niden\Models\Companies;
use Niden\Models\Products;
use Niden\Models\ProductTypes;
use Niden\Models\Users;
use Page\Data;
use function uniqid;

class GetCest
{
    public function getProduct(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();

        /** @var ProductTypes $productType */
        $productType = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'prt_name'        => uniqid('prt-a-'),
                'prt_description' => uniqid(),
            ]
        );

        /** @var Products $product */
        $product = $I->haveRecordWithFields(
            Products::class,
            [
                'prd_name'        => uniqid('prd-a-'),
                'prd_prt_id'      => $productType->get('prt_id'),
                'prd_description' => uniqid(),
                'prd_quantity'    => 25,
                'prd_price'       => 19.99,
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productsUrl . '/' . $product->get('prd_id'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $product->get('prd_id'),
                    'type'       => Resources::PRODUCTS,
                    'attributes' => [
                        'name'        => $product->get('prd_name'),
                        'description' => $product->get('prd_description'),
                        'quantity'    => $product->get('prd_quantity'),
                        'price'       => $product->get('prd_price'),
                    ],
                ],
            ]
        );
    }

    public function getProducts(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();

        /** @var ProductTypes $productType */
        $productType = $I->haveRecordWithFields(
            ProductTypes::class,
            [
                'prt_name'        => uniqid('prt-a-'),
                'prt_description' => uniqid(),
            ]
        );

        /** @var Products $productOne */
        $productOne = $I->haveRecordWithFields(
            Products::class,
            [
                'prd_name'        => uniqid('prd-a-'),
                'prd_prt_id'      => $productType->get('prt_id'),
                'prd_description' => uniqid(),
                'prd_quantity'    => 25,
                'prd_price'       => 19.99,
            ]
        );

        /** @var Products $productTwo */
        $productTwo = $I->haveRecordWithFields(
            Products::class,
            [
                'prd_name'        => uniqid('prd-b-'),
                'prd_prt_id'      => $productType->get('prt_id'),
                'prd_description' => uniqid(),
                'prd_quantity'    => 25,
                'prd_price'       => 19.99,
            ]
        );

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            [
                [
                    'id'         => $productOne->get('prd_id'),
                    'type'       => Resources::PRODUCTS,
                    'attributes' => [
                        'name'        => $productOne->get('prd_name'),
                        'description' => $productOne->get('prd_description'),
                        'quantity'    => $productOne->get('prd_quantity'),
                        'price'       => $productOne->get('prd_price'),
                    ],
                ],
                [
                    'id'         => $productTwo->get('prd_id'),
                    'type'       => Resources::PRODUCTS,
                    'attributes' => [
                        'name'        => $productTwo->get('prd_name'),
                        'description' => $productTwo->get('prd_description'),
                        'quantity'    => $productTwo->get('prd_quantity'),
                        'price'       => $productTwo->get('prd_price'),
                    ],
                ],
            ]
        );
    }

    public function getProductsNoData(ApiTester $I)
    {
        $this->addRecord($I);
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$productsUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse();
    }

    private function addRecord(ApiTester $I)
    {
        return $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag'    => 1,
                'usr_username'       => 'testuser',
                'usr_password'       => 'testpassword',
                'usr_issuer'         => 'https://niden.net',
                'usr_token_password' => '12345',
                'usr_token_id'       => '110011',
            ]
        );
    }
}

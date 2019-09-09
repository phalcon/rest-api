<?php

namespace Phalcon\Api\Tests\api\Products;

use ApiTester;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Page\Data;
use function Phalcon\Api\Core\envValue;

class GetCest
{
    /**
     * @param ApiTester $I
     *
     * @throws ModelException
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
     * @throws ModelException
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
     */
    public function getProductsWithIncludesAllIncludes(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::COMPANIES, Relationships::PRODUCT_TYPES]);
    }

    /**
     * @param ApiTester $I
     */
    public function getProductsWithIncludesCompanies(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::COMPANIES]);
    }

    /**
     * @param ApiTester $I
     */
    public function getProductsWithIncludesProductTypes(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::PRODUCT_TYPES]);
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

    private function checkIncludes(ApiTester $I, array $includes = [])
    {
        list($product, $productType, $comOne, $comTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                Data::$productsRecordIncludesUrl,
                $product->get('id'),
                implode(',', $includes)
            )
        );
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();

        $element = [
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
                            'id'   => $comOne->get('id'),
                        ],
                        [
                            'type' => Relationships::COMPANIES,
                            'id'   => $comTwo->get('id'),
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
                        'id'   => $productType->get('id'),
                    ],
                ];

                $included[] = Data::productTypeResponse($productType);

            }
        }

        $I->seeSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $I->seeSuccessJsonResponse('included', $included);
        }
    }
}

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
use function sprintf;
use function uniqid;

class GetCest
{
    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
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
        $I->sendGET(sprintf(Data::$companiesRecordUrl, $comOne->get('id')));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($comOne),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getUnknownCompany(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesRecordUrl, 9999));
        $I->deleteHeader('Authorization');
        $I->seeResponseIs404();
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function getCompanies(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token  = $I->apiLogin();
        $comOne = $I->addCompanyRecord('com-a-');
        $comTwo = $I->addCompanyRecord('com-b-');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(Data::$companiesUrl);
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companyResponse($comOne),
                Data::companyResponse($comTwo),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesWithRelationshipProducts(ApiTester $I)
    {
        $this->runCompaniesWithProductsTests($I, Data::$companiesRecordRelationshipRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesWithProducts(ApiTester $I)
    {
        $this->runCompaniesWithProductsTests($I, Data::$companiesRecordRelationshipUrl);
    }

    /**
     * @param ApiTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
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

    /**
     * @param ApiTester $I
     */
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

    /**
     * @param ApiTester $I
     * @param string    $url
     *
     * @throws \Niden\Exception\ModelException
     */
    private function runCompaniesWithProductsTests(ApiTester $I, $url = '')
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /** @var ProductTypes $productType */
        $productType = $I->addProductTypeRecord('prt-a-');
        /** @var Products $productOne */
        $productOne = $I->addProductRecord('prd-a-', $productType->get('id'));
        /** @var Products $productTwo */
        $productTwo = $I->addProductRecord('prd-b-', $productType->get('id'));
        /** @var Companies $comOne */
        $comOne     = $I->addCompanyRecord('com-a-');

        $I->addCompanyXProduct($comOne->get('id'), $productOne->get('id'));
        $I->addCompanyXProduct($comOne->get('id'), $productTwo->get('id'));

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                $url,
                $comOne->get('id'),
            Relationships::PRODUCTS
            )
        );
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
                Data::productResponse($productOne),
                Data::productResponse($productTwo),
            ]
        );
    }
}


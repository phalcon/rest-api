<?php

namespace Page;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Phalcon\Api\Mvc\Model\AbstractModel;

use function Phalcon\Api\Core\envValue;
use function sprintf;

class Data
{
    public static $companiesSortUrl                 = '/companies?sort=%s';
    public static $companiesUrl                     = '/companies';
    public static $companiesRecordUrl               = '/companies/%s';
    public static $companiesRecordIncludesUrl       = '/companies/%s?includes=%s';
    public static $loginUrl                         = '/login';
    public static $individualsUrl                   = '/individuals';
    public static $individualsRecordUrl             = '/individuals/%s';
    public static $individualsRecordIncludesUrl     = '/individuals/%s?includes=%s';
    public static $individualTypesUrl               = '/individual-types';
    public static $individualTypesRecordUrl         = '/individual-types/%s';
    public static $individualTypesRecordIncludesUrl = '/individual-types/%s?includes=%s';
    public static $productsUrl                      = '/products';
    public static $productsRecordUrl                = '/products/%s';
    public static $productsRecordIncludesUrl        = '/products/%s?includes=%s';
    public static $productTypesUrl                  = '/product-types';
    public static $productTypesRecordUrl            = '/product-types/%s';
    public static $productTypesRecordIncludesUrl    = '/product-types/%s?includes=%s';
    public static $usersUrl                         = '/users';
    public static $wrongUrl                         = '/sommething';

    public static $strongPassphrase  = 'DR^3*ZwnAHKc9yP$YSpW98dsmHJBax5&';
    public static $testIssuer        = 'https://niden.net';
    public static $testPassword      = 'testpass';
    public static $testTokenId       = '110011';
    public static $testTokenPassword = 'DR^4*ZwnAHKc0yP$YSpW09dsmHJBax6&';
    public static $testUsername      = 'testuser';

    /**
     * @return array
     */
    public static function loginJson()
    {
        return [
            'username' => self::$testUsername,
            'password' => self::$testPassword,
        ];
    }

    /**
     * @param        $name
     * @param string $address
     * @param string $city
     * @param string $phone
     *
     * @return array
     */
    public static function companyAddJson($name, $address = '', $city = '', $phone = '')
    {
        return [
            'name'    => $name,
            'address' => $address,
            'city'    => $city,
            'phone'   => $phone,
        ];
    }

    /**
     * @param Companies $record
     *
     * @return array
     * @throws ModelException
     */
    public static function companiesAddResponse(Companies $record): array
    {
        return [
            'type'       => Relationships::COMPANIES,
            'id'         => (string) $record->get('id'),
            'attributes' => [
                'name'    => $record->get('name'),
                'address' => $record->get('address'),
                'city'    => $record->get('city'),
                'phone'   => $record->get('phone'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::COMPANIES,
                    $record->get('id')
                ),
            ],
        ];
    }

    /**
     * @param Companies $record
     *
     * @return array
     * @throws ModelException
     */
    public static function companiesResponse(Companies $record): array
    {
        return [
            'type'          => Relationships::COMPANIES,
            'id'            => (string) $record->get('id'),
            'attributes'    => [
                'name'    => $record->get('name'),
                'address' => $record->get('address'),
                'city'    => $record->get('city'),
                'phone'   => $record->get('phone'),
            ],
            'links'         => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::COMPANIES,
                    $record->get('id')
                ),
            ],
            'relationships' => [
                Relationships::PRODUCTS    => [
                    'links' => [
                        'self'    => sprintf(
                            '%s/%s/%s/relationships/products',
                            envValue('APP_URL'),
                            Relationships::COMPANIES,
                            $record->get('id')
                        ),
                        'related' => sprintf(
                            '%s/%s/%s/products',
                            envValue('APP_URL'),
                            Relationships::COMPANIES,
                            $record->get('id')
                        ),
                    ]
                ],
                Relationships::INDIVIDUALS => [
                    "links" => [
                        'self'    => sprintf(
                            '%s/%s/%s/relationships/individuals',
                            envValue('APP_URL'),
                            Relationships::COMPANIES,
                            $record->get('id')
                        ),
                        'related' => sprintf(
                            '%s/%s/%s/individuals',
                            envValue('APP_URL'),
                            Relationships::COMPANIES,
                            $record->get('id')
                        ),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param Individuals $record
     *
     * @return array
     * @throws ModelException
     */
    public static function individualResponse(Individuals $record): array
    {
        return [
            'type'       => Relationships::INDIVIDUALS,
            'id'         => (string) $record->get('id'),
            'attributes' => [
                'companyId' => $record->get('companyId'),
                'typeId'    => $record->get('typeId'),
                'prefix'    => $record->get('prefix'),
                'first'     => $record->get('first'),
                'middle'    => $record->get('middle'),
                'last'      => $record->get('last'),
                'suffix'    => $record->get('suffix'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::INDIVIDUALS,
                    $record->get('id')
                ),
            ],
        ];
    }

    /**
     * @param IndividualTypes $record
     *
     * @return array
     * @throws ModelException
     */
    public static function individualTypeResponse(IndividualTypes $record): array
    {
        return [
            'type'       => Relationships::INDIVIDUAL_TYPES,
            'id'         => (string) $record->get('id'),
            'attributes' => [
                'name'        => $record->get('name'),
                'description' => $record->get('description'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::INDIVIDUAL_TYPES,
                    $record->get('id')
                ),
            ],
        ];
    }

    /**
     * @param Products $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productResponse(Products $record): array
    {
        return [
            'type'       => Relationships::PRODUCTS,
            'id'         => (string) $record->get('id'),
            'attributes' => [
                'typeId'      => $record->get('typeId'),
                'name'        => $record->get('name'),
                'description' => $record->get('description'),
                'quantity'    => $record->get('quantity'),
                'price'       => $record->get('price'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::PRODUCTS,
                    $record->get('id')
                ),
            ],
        ];
    }

    /**
     * @param Products $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productFieldsResponse(Products $record): array
    {
        return [
            'type'       => Relationships::PRODUCTS,
            'id'         => (string) $record->get('id'),
            'attributes' => [
                'name'  => $record->get('name'),
                'price' => $record->get('price'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::PRODUCTS,
                    $record->get('id')
                ),
            ],
        ];
    }

    /**
     * @param ProductTypes $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productTypeResponse(ProductTypes $record): array
    {
        return [
            'type'       => Relationships::PRODUCT_TYPES,
            'id'         => (string) $record->get('id'),
            'attributes' => [
                'name'        => $record->get('name'),
                'description' => $record->get('description'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::PRODUCT_TYPES,
                    $record->get('id')
                ),
            ],
        ];
    }

    /**
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
    public static function userResponse(AbstractModel $record)
    {
        return [
            'type'       => Relationships::USERS,
            'id'         => (string) $record->get('id'),
            'attributes' => [
                'status'        => $record->get('status'),
                'username'      => $record->get('username'),
                'issuer'        => $record->get('issuer'),
                'tokenPassword' => $record->get('tokenPassword'),
                'tokenId'       => $record->get('tokenId'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::USERS,
                    $record->get('id')
                ),
            ],
        ];
    }
}

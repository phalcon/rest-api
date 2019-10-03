<?php

namespace Page;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Mvc\Model\AbstractModel;
use function Phalcon\Api\Core\envValue;

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

    /**
     * @return array
     */
    public static function loginJson()
    {
        return [
            'username' => 'testuser',
            'password' => 'testpassword',
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
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
    public static function companiesResponse(AbstractModel $record)
    {
        return [
            'id'         => $record->get('id'),
            'type'       => Relationships::COMPANIES,
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
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
    public static function individualResponse(AbstractModel $record)
    {
        return [
            'id'         => $record->get('id'),
            'type'       => Relationships::INDIVIDUALS,
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
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
    public static function individualTypeResponse(AbstractModel $record)
    {
        return [
            'id'         => $record->get('id'),
            'type'       => Relationships::INDIVIDUAL_TYPES,
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
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productResponse(AbstractModel $record)
    {
        return [
            'type'       => Relationships::PRODUCTS,
            'id'         => $record->get('id'),
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
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productFieldsResponse(AbstractModel $record)
    {
        return [
            'type'       => Relationships::PRODUCTS,
            'id'         => $record->get('id'),
            'attributes' => [
                'name'        => $record->get('name'),
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
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productTypeResponse(AbstractModel $record)
    {
        return [
            'id'         => $record->get('id'),
            'type'       => Relationships::PRODUCT_TYPES,
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
            'id'         => $record->get('id'),
            'type'       => Relationships::USERS,
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

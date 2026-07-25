<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Tests\Support;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Phalcon\Api\Mvc\Model\AbstractModel;

use function Phalcon\Api\Core\appUrl;

class Data
{
    public static $companiesRecordIncludesUrl       = '/companies/%s?includes=%s';
    public static $companiesRecordUrl               = '/companies/%s';
    public static $companiesSortUrl                 = '/companies?sort=%s';
    public static $companiesUrl                     = '/companies';
    public static $individualsRecordIncludesUrl     = '/individuals/%s?includes=%s';
    public static $individualsRecordUrl             = '/individuals/%s';
    public static $individualsUrl                   = '/individuals';
    public static $individualTypesRecordIncludesUrl = '/individual-types/%s?includes=%s';
    public static $individualTypesRecordUrl         = '/individual-types/%s';
    public static $individualTypesUrl               = '/individual-types';
    public static $loginUrl                         = '/login';
    public static $productsRecordIncludesUrl        = '/products/%s?includes=%s';
    public static $productsRecordUrl                = '/products/%s';
    public static $productsUrl                      = '/products';
    public static $productTypesRecordIncludesUrl    = '/product-types/%s?includes=%s';
    public static $productTypesRecordUrl            = '/product-types/%s';
    public static $productTypesUrl                  = '/product-types';

    public static $strongPassphrase = 'DR^3*ZwnAHKc9yP$YSpW98dsmHJBax5&';
    public static $testIssuer       = 'https://niden.net';
    public static $testPassword     = 'testpass';
    /**
     * The bcrypt hash of $testPassword. Hardcoded on purpose: fixtures store
     * the hash, requests send the plain password, and hashing on every insert
     * would cost a bcrypt round per fixture for no benefit.
     */
    public static $testPasswordHash    = '$2y$10$DSCDlw9tZtmQikTY8cwbGuUZSMcPo64YfRYCTREygVUMJTDqjTHFu';
    public static $testTokenId         = '110011';
    public static $testTokenPassword   = 'DR^4*ZwnAHKc0yP$YSpW09dsmHJBax6&';
    public static $testUsername        = 'testuser';
    public static $usersUrl                         = '/users';
    public static $wrongUrl                         = '/sommething';

    /**
     * @param Companies $record
     *
     * @return array
     * @throws ModelException
     */
    public static function companiesAddResponse(Companies $record): array
    {
        return self::resource(
            Relationships::COMPANIES,
            $record,
            [
                'name'    => $record->get('name'),
                'address' => $record->get('address'),
                'city'    => $record->get('city'),
                'phone'   => $record->get('phone'),
            ]
        );
    }

    /**
     * @param Companies $record
     *
     * @return array
     * @throws ModelException
     */
    public static function companiesResponse(Companies $record): array
    {
        $recordId = $record->get('id');

        return self::resource(
            Relationships::COMPANIES,
            $record,
            [
                'name'    => $record->get('name'),
                'address' => $record->get('address'),
                'city'    => $record->get('city'),
                'phone'   => $record->get('phone'),
            ]
        ) + [
            'relationships' => [
                Relationships::PRODUCTS    => [
                    'links' => self::relationshipLinks(
                        Relationships::COMPANIES,
                        $recordId,
                        Relationships::PRODUCTS
                    ),
                ],
                Relationships::INDIVIDUALS => [
                    'links' => self::relationshipLinks(
                        Relationships::COMPANIES,
                        $recordId,
                        Relationships::INDIVIDUALS
                    ),
                ],
            ],
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
     * @param Individuals $record
     *
     * @return array
     * @throws ModelException
     */
    public static function individualResponse(Individuals $record): array
    {
        return self::resource(
            Relationships::INDIVIDUALS,
            $record,
            [
                'companyId' => $record->get('companyId'),
                'typeId'    => $record->get('typeId'),
                'prefix'    => $record->get('prefix'),
                'first'     => $record->get('first'),
                'middle'    => $record->get('middle'),
                'last'      => $record->get('last'),
                'suffix'    => $record->get('suffix'),
            ]
        );
    }

    /**
     * @param IndividualTypes $record
     *
     * @return array
     * @throws ModelException
     */
    public static function individualTypeResponse(IndividualTypes $record): array
    {
        return self::resource(
            Relationships::INDIVIDUAL_TYPES,
            $record,
            [
                'name'        => $record->get('name'),
                'description' => $record->get('description'),
            ]
        );
    }

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
     * @param Products $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productFieldsResponse(Products $record): array
    {
        return self::resource(
            Relationships::PRODUCTS,
            $record,
            [
                'name'  => $record->get('name'),
                'price' => $record->get('price'),
            ]
        );
    }

    /**
     * @param Products $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productResponse(Products $record): array
    {
        return self::resource(
            Relationships::PRODUCTS,
            $record,
            [
                'typeId'      => $record->get('typeId'),
                'name'        => $record->get('name'),
                'description' => $record->get('description'),
                'quantity'    => $record->get('quantity'),
                'price'       => $record->get('price'),
            ]
        );
    }

    /**
     * @param ProductTypes $record
     *
     * @return array
     * @throws ModelException
     */
    public static function productTypeResponse(ProductTypes $record): array
    {
        return self::resource(
            Relationships::PRODUCT_TYPES,
            $record,
            [
                'name'        => $record->get('name'),
                'description' => $record->get('description'),
            ]
        );
    }

    /**
     * The self/related pair a relationship carries.
     *
     * @param string $type
     * @param mixed  $recordId
     * @param string $relationship
     *
     * @return array
     */
    public static function relationshipLinks(
        string $type,
        $recordId,
        string $relationship
    ): array {
        $base = appUrl($type, (int) $recordId);

        return [
            'self'    => $base . '/relationships/' . $relationship,
            'related' => $base . '/' . $relationship,
        ];
    }

    /**
     * The JSON:API envelope every resource shares - type, id, attributes and a
     * self link. Only the attributes differ between resources, so only the
     * attributes are worth stating at each call site.
     *
     * @param string        $type
     * @param AbstractModel $record
     * @param array         $attributes
     *
     * @return array
     * @throws ModelException
     */
    public static function resource(
        string $type,
        AbstractModel $record,
        array $attributes
    ): array {
        return [
            'type'       => $type,
            'id'         => (string) $record->get('id'),
            'attributes' => $attributes,
            'links'      => [
                'self' => appUrl($type, (int) $record->get('id')),
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
        return self::resource(
            Relationships::USERS,
            $record,
            [
                'status'   => $record->get('status'),
                'username' => $record->get('username'),
                'issuer'   => $record->get('issuer'),
                'tokenId'  => $record->get('tokenId'),
            ]
        );
    }
}

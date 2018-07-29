<?php

namespace Page;

class Data
{
    public static $companiesUrl                                     = '/companies';
    public static $companiesRecordUrl                               = '/companies/%';
    public static $companiesRecordRelationshipUrl                   = '/companies/%/%';
    public static $companiesRecordRelationshipRelationshipUrl       = '/companies/%/relationships/%s';
    public static $loginUrl                                         = '/login';
    public static $individualTypesUrl                               = '/individual-types';
    public static $individualTypesRecordUrl                         = '/individual-types/%';
    public static $individualTypesRecordRelationshipUrl             = '/individual-types/%/%';
    public static $individualTypesRecordRelationshipRelationshipUrl = '/individual-types/%/relationships/%s';
    public static $productsUrl                                      = '/products';
    public static $productsRecordUrl                                = '/products/%';
    public static $productsRecordRelationshipUrl                    = '/products/%/%';
    public static $productsRecordRelationshipRelationshipUrl        = '/products/%/relationships/%s';
    public static $productTypesUrl                                  = '/product-types';
    public static $productTypesRecordUrl                            = '/product-types/%';
    public static $productTypesRecordRelationshipUrl                = '/product-types/%/%';
    public static $productTypesRecordRelationshipRelationshipUrl    = '/product-types/%/relationships/%s';
    public static $usersUrl                                         = '/users';
    public static $wrongUrl                                         = '/sommething';

    public static function loginJson()
    {
        return [
            'username' => 'testuser',
            'password' => 'testpassword',
        ];
    }

    public static function companyAddJson($name, $address = '', $city = '', $phone = '')
    {
        return [
            'name'    => $name,
            'address' => $address,
            'city'    => $city,
            'phone'   => $phone,
        ];
    }
}

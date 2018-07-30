<?php

namespace Page;

class Data
{
    public static $companiesUrl                                     = '/companies';
    public static $companiesRecordUrl                               = '/companies/%s';
    public static $companiesRecordRelationshipUrl                   = '/companies/%s/%s';
    public static $companiesRecordRelationshipRelationshipUrl       = '/companies/%s/relationships/%s';
    public static $loginUrl                                         = '/login';
    public static $individualTypesUrl                               = '/individual-types';
    public static $individualTypesRecordUrl                         = '/individual-types/%s';
    public static $individualTypesRecordRelationshipUrl             = '/individual-types/%s/%s';
    public static $individualTypesRecordRelationshipRelationshipUrl = '/individual-types/%s/relationships/%s';
    public static $productsUrl                                      = '/products';
    public static $productsRecordUrl                                = '/products/%s';
    public static $productsRecordRelationshipUrl                    = '/products/%s/%s';
    public static $productsRecordRelationshipRelationshipUrl        = '/products/%s/relationships/%s';
    public static $productTypesUrl                                  = '/product-types';
    public static $productTypesRecordUrl                            = '/product-types/%s';
    public static $productTypesRecordRelationshipUrl                = '/product-types/%s/%s';
    public static $productTypesRecordRelationshipRelationshipUrl    = '/product-types/%s/relationships/%s';
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

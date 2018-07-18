<?php

namespace Page;

class Data
{
    public static $companiesUrl       = '/companies';
    public static $loginUrl           = '/login';
    public static $individualTypesUrl = '/individualtypes';
    public static $productTypesUrl    = '/producttypes';
    public static $usersUrl           = '/users';
    public static $wrongUrl           = '/sommething';

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

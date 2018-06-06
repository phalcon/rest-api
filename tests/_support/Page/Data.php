<?php

namespace Page;

class Data
{
    public static $loginUrl    = '/login';
    public static $rootUrl     = '/';
    public static $userGetUrl  = '/user/get';
    public static $usersGetUrl = '/users/get';
    public static $wrongUrl    = '/sommething';

    public static function loginJson()
    {
        return json_encode(
            [
                'data' => [
                    'username' => 'testuser',
                    'password' => 'testpassword',
                ]
            ]
        );
    }

    public static function userGetJson($userId)
    {
        return json_encode(
            [
                'data' => [
                    'userId' => $userId,
                ]
            ]
        );
    }
}

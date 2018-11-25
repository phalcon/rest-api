<?php

namespace Page;

class Data
{
    public static $loginUrl = '/v1/auth';
    public static $usersUrl = '/users';
    public static $wrongUrl = '/baka';

    /**
     * @return array
     */
    public static function loginJson()
    {
        return [
            'email' => 'baka@mctekk.com',
            'password' => '1234567890',
        ];
    }
}

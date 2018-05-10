<?php

namespace Niden\Tests\integration\Models;

use IntegrationTester;
use Niden\Models\Users;


class UsersCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Users::class,
            [
                'usr_id',
                'usr_status_flag',
                'usr_username',
                'usr_password',
                'usr_domain_name',
                'usr_token',
                'usr_token_id',
            ]
        );
    }
}

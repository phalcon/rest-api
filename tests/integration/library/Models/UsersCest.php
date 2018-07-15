<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use Lcobucci\JWT\ValidationData;
use Niden\Models\Users;
use Niden\Traits\TokenTrait;
use Phalcon\Filter;

class UsersCest
{
    use TokenTrait;

    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Users::class,
            [
                'usr_id',
                'usr_status_flag',
                'usr_username',
                'usr_password',
                'usr_issuer',
                'usr_token_password',
                'usr_token_id',
            ]
        );
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new Users();
        $expected = [
            'usr_id'             => Filter::FILTER_ABSINT,
            'usr_status_flag'    => Filter::FILTER_ABSINT,
            'usr_username'       => Filter::FILTER_STRING,
            'usr_password'       => Filter::FILTER_STRING,
            'usr_issuer'         => Filter::FILTER_STRING,
            'usr_token_password' => Filter::FILTER_STRING,
            'usr_token_id'       => Filter::FILTER_STRING,
        ];
        $I->assertEquals($expected, $model->getModelFilters());
    }

    public function validatePrefix(IntegrationTester $I)
    {
        $model = new Users();
        $I->assertEquals('usr', $model->getTablePrefix());
    }

    public function checkValidationData(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields(
            Users::class,
            [
                'usr_status_flag'    => 1,
                'usr_username'       => 'testuser',
                'usr_password'       => 'testpassword',
                'usr_issuer'         => 'https://niden.net',
                'usr_token_password' => '12345',
                'usr_token_id'       => '110011',
            ]
        );

        $validationData = new ValidationData();
        $validationData->setIssuer('https://niden.net');
        $validationData->setAudience($this->getTokenAudience());
        $validationData->setId('110011');
        $validationData->setCurrentTime(time() + 10);

        $I->assertEquals($validationData, $user->getValidationData());
    }
}

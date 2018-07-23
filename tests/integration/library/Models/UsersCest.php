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
            'id'            => Filter::FILTER_ABSINT,
            'status'        => Filter::FILTER_ABSINT,
            'username'      => Filter::FILTER_STRING,
            'password'      => Filter::FILTER_STRING,
            'issuer'        => Filter::FILTER_STRING,
            'tokenPassword' => Filter::FILTER_STRING,
            'tokenId'       => Filter::FILTER_STRING,
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
                'username'      => 'testuser',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'https://niden.net',
                'tokenPassword' => '12345',
                'tokenId'       => '110011',
            ]
        );

        $validationData = new ValidationData();
        $validationData->setIssuer('https://niden.net');
        $validationData->setAudience($this->getTokenAudience());
        $validationData->setId('110011');
        $validationData->setCurrentTime(time() + 10);

        $I->assertEquals($validationData, $user->getValidationData());
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual = $I->getModelRelationships(Users::class);
        $I->assertEquals(0, count($actual));
    }
}

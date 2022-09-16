<?php

namespace Phalcon\Api\Tests\integration\library\Models;

use IntegrationTester;
use Page\Data;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use Phalcon\Filter\Filter;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Validator;

class UsersCest
{
    use TokenTrait;

    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition(
            Users::class,
            [
                'id',
                'status',
                'username',
                'password',
                'issuer',
                'tokenPassword',
                'tokenId',
            ]
        );
    }

    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
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
        $I->assertSame($expected, $model->getModelFilters());
    }

    /**
     * @param IntegrationTester $I
     *
     * @return void
     * @throws ModelException
     * @throws ValidatorException
     */
    public function checkValidationData(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields(
            Users::class,
            [
                'username'      => Data::$testUsername,
                'password'      => Data::$testPassword,
                'status'        => 1,
                'issuer'        => 'https://niden.net',
                'tokenPassword' => Data::$strongPassphrase,
                'tokenId'       => Data::$testTokenId,
            ]
        );

        $signer  = new Hmac();
        $builder = new Builder($signer);
        $token   = $builder
            ->setIssuer('https://niden.net')
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setExpirationTime(time() + 10)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $class  = Validator::class;
        $actual = $user->getValidationData();
        $I->assertInstanceOf($class, $actual);
    }

    /**
     * @param IntegrationTester $I
     *
     * @return void
     */
    public function validateRelationships(IntegrationTester $I)
    {
        $actual = $I->getModelRelationships(Users::class);
        $I->assertSame(0, count($actual));
    }
}

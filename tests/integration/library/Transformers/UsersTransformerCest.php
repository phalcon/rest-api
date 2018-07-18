<?php

namespace Niden\Tests\integration\library\Transformers;

use IntegrationTester;
use Niden\Constants\Resources;
use Niden\Models\Users;
use Niden\Transformers\UsersTransformer;

class UsersTransformerCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws \Niden\Exception\ModelException
     */
    public function checkTransformer(IntegrationTester $I)
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

        $transformer = new UsersTransformer();
        $expected = [
            'id'         => $user->get('usr_id'),
            'type'       => Resources::USERS,
            'attributes' => [
                'status'        => $user->get('usr_status_flag'),
                'username'      => $user->get('usr_username'),
                'issuer'        => $user->get('usr_issuer'),
                'tokenPassword' => $user->get('usr_token_password'),
                'tokenId'       => $user->get('usr_token_id'),
            ],
        ];

        $I->assertEquals($expected, $transformer->transform($user));
    }
}

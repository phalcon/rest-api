<?php 

class UsersInviteCest
{
    public function insertInvite(ApiTester $I):void
    {
        $userData = $I->apiLogin();
        $testEmail = 'testMC@example.com';

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/users/invite', [
            'email' => $testEmail,
            'role' => 'Admins'
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['email'] == $testEmail);
    }

    public function processUserInvite(ApiTester $I):void
    {
        $userData = $I->apiLogin();
        $testEmail = 'testMC@example.com';

        //Buscar email hash en la base de datos poner luego en url.

        $I->haveHttpHeader('Authorization', $userData->token);

        $I->sendGet(`/v1/users-invite`);
        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['email'] == $testEmail);

        // $hash =  $data['invite_hash'];

        // $I->sendPost(`/v1/users/invite/insert?hash=$hash`, [
        //     'firstname' => 'testFirstName',
        //     'lastname' => 'testLastName',
        //     'displayname' => 'testDisplayName',
        //     'password' => 'testpassword',
        // ]);

        // $I->seeResponseIsSuccessful();
        // $response = $I->grabResponse();
        // $data = json_decode($response, true);

        // $I->assertTrue($data['email'] == $testEmail);
    }
}

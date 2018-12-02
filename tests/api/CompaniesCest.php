<?php

namespace Gewaer\Tests\api;

use ApiTester;
use Phalcon\Security\Random;

class CompaniesCest extends BakaRestTest
{
    protected $model = 'companies';

    /**
     * Create
     *
     * @param ApiTester $I
     * @return void
     */
    public function create(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $companyName = $random->base58();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/' . $this->model, [
            'name' => $companyName
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['name'] == $companyName);
    }

    /**
     * update
     *
     * @param ApiTester $I
     * @return void
     */
    public function update(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $companyName = $random->base58();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet('/v1/' . $this->model);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->sendPUT('/v1/' . $this->model . '/' . $data[count($data) - 1]['id'], [
            'name' => $companyName
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['name'] == $companyName);
    }
}

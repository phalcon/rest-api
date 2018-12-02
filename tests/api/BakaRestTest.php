<?php

namespace Gewaer\Tests\api;

use ApiTester;

/**
 * Baka Rest standar functions
 *
 * @package Gewaer\Tests\api
 */
abstract class BakaRestTest
{
    protected $model;

    /**
     * Create
     *
     * @param ApiTester $I
     * @return void
     */
    abstract public function create(ApiTester $I) : void;

    /**
     * Update
     *
     * @param ApiTester $I
     * @return void
     */
    abstract public function update(ApiTester $I) : void;

    /**
     * Get
     *
     * @param ApiTester $I
     * @return void
     */
    public function list(ApiTester $I) : void
    {
        $userData = $I->apiLogin();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet("/v1/{$this->model}");

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue(isset($data[0]['id']));
    }

    /**
     * Get
     *
     * @param ApiTester $I
     * @return void
     */
    public function getById(ApiTester $I) : void
    {
        $userData = $I->apiLogin();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet("/v1/{$this->model}");

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->sendGet("/v1/{$this->model}/" . $data[0]['id']);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue(isset($data['id']));
    }

    /**
     * Delete
     *
     * @param ApiTester $I
     * @return void
     */
    public function delete(ApiTester $I) : void
    {
        $userData = $I->apiLogin();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet("/v1/{$this->model}");

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->sendDELETE("/v1/{$this->model}/" . $data[count($data) - 1]['id']);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data[0] == 'Delete Successfully');
    }
}

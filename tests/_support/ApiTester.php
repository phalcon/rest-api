<?php

use Codeception\Util\HttpCode;
use Niden\Http\Response;
use Niden\Models\Users;
use Page\Data as DataPage;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    /**
     * Checks if the response was successful
     */
    public function seeResponseIsSuccessful()
    {
        $this->seeResponseIsJson();
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseMatchesJsonType(
            [
                'jsonapi' => [
                    'version' => 'string'
                ],
//                'data'    => 'array',
//                'errors'  => 'array',
                'meta'    => [
                    'timestamp' => 'string:date',
                    'hash'      => 'string',
                ]
            ]
        );

        $response  = $this->grabResponse();
        $response  = json_decode($response, true);
        $timestamp = $response['meta']['timestamp'];
        $hash      = $response['meta']['hash'];
        unset($response['meta'], $response['jsonapi']);
        $this->assertEquals($hash, sha1($timestamp . json_encode($response)));
    }

    /**
     * Checks if the response was successful
     */
    public function seeResponseIs404()
    {
        $this->seeResponseIsJson();
        $this->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $this->seeResponseMatchesJsonType(
            [
                'jsonapi' => [
                    'version' => 'string'
                ],
                'meta'    => [
                    'timestamp' => 'string:date',
                    'hash'      => 'string',
                ]
            ]
        );

        $response  = $this->grabResponse();
        $response  = json_decode($response, true);
        $timestamp = $response['meta']['timestamp'];
        $hash      = $response['meta']['hash'];
        $this->assertEquals('Not Found', $response['errors'][0]);
        unset($response['jsonapi'], $response['meta']);
        $this->assertEquals($hash, sha1($timestamp . json_encode($response)));
    }

    public function seeErrorJsonResponse(string $message)
    {
        $this->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'errors' => [
                    $message,
                ],
            ]
        );
    }

    public function seeSuccessJsonResponse(string $key = 'data', array $data = [])
    {
        $this->seeResponseContainsJson([$key => $data]);
    }

    public function apiLogin()
    {
        $this->deleteHeader('Authorization');
        $this->sendPOST(DataPage::$loginUrl, DataPage::loginJson());
        $this->seeResponseIsSuccessful();

        $response = $this->grabResponse();
        $response  = json_decode($response, true);
        $data      = $response['data'];
        $token     = $data['token'];

        return $token;
    }

    public function addApiUserRecord()
    {
        return $this->haveRecordWithFields(
            Users::class,
            [
                'status'        => 1,
                'username'      => 'testuser',
                'password'      => 'testpassword',
                'issuer'        => 'https://niden.net',
                'tokenPassword' => '12345',
                'tokenId'       => '110011',
            ]
        );
    }
}

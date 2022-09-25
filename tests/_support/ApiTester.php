<?php

use Codeception\Actor;
use Codeception\Lib\Friend;
use Codeception\Util\HttpCode;
use Page\Data as DataPage;
use Phalcon\Api\Constants\Flags;
use Phalcon\Api\Models\Users;

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
 * @method Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    /**
     * Checks if the response was successful
     *
     * @param int $code
     *
     * @return void
     */
    public function seeResponseIsSuccessful(int $code = HttpCode::OK): void
    {
        $this->seeResponseIsJson();
        $this->seeResponseCodeIs($code);
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

        $this->checkHash();
    }

    /**
     * Checks if the response was successful
     *
     * @return void
     */
    public function seeResponseIs400(): void
    {
        $this->checkErrorResponse(HttpCode::BAD_REQUEST);
    }

    /**
     * Checks if the response was successful
     *
     * @return void
     */
    public function seeResponseIs404(): void
    {
        $this->checkErrorResponse(HttpCode::NOT_FOUND);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function seeErrorJsonResponse(string $message)
    {
        $this->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'errors'  => [
                    $message,
                ],
            ]
        );
    }

    /**
     * @param string $key
     * @param array  $data
     *
     * @return void
     */
    public function seeSuccessJsonResponse(string $key = 'data', array $data = [])
    {
        $this->seeResponseContainsJson([$key => $data]);
    }

    /**
     * @return mixed|string
     */
    public function apiLogin()
    {
        $this->deleteHeader('Authorization');
        $this->sendPOST(DataPage::$loginUrl, DataPage::loginJson());
        $this->seeResponseIsSuccessful();

        $response = $this->grabResponse();
        $response = json_decode($response, true);
        $data     = $response['data'] ?? [];
        $token    = $data['token'] ?? '';

        return $token;
    }

    /**
     * @return mixed
     */
    public function addApiUserRecord()
    {
        return $this->haveRecordWithFields(
            Users::class,
            [
                'status'        => Flags::ACTIVE,
                'username'      => DataPage::$testUsername,
                'password'      => DataPage::$testPassword,
                'issuer'        => DataPage::$testIssuer,
                'tokenPassword' => DataPage::$testTokenPassword,
                'tokenId'       => DataPage::$testTokenId,
            ]
        );
    }

    /**
     * @return void
     */
    private function checkHash(): void
    {
        $response  = $this->grabResponse();
        $response  = json_decode($response, true);
        $timestamp = $response['meta']['timestamp'];
        $hash      = $response['meta']['hash'];
        unset($response['meta'], $response['jsonapi']);

        $actual = sha1($timestamp . json_encode($response));
        $this->assertEquals($hash, $actual);
    }

    /**
     * @param int $code
     *
     * @return void
     */
    private function checkErrorResponse(int $code): void
    {
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
        $this->assertEquals(HttpCode::getDescription($code), $response['errors'][0]);
        unset($response['jsonapi'], $response['meta']);
        $this->assertEquals($hash, sha1($timestamp . json_encode($response)));


        $this->seeResponseIsJson();
        $this->seeResponseCodeIs($code);
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

        $this->checkHash();
    }
}

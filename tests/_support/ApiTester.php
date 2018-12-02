<?php

use Codeception\Util\HttpCode;
use Niden\Http\Response;
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
    public function seeResponseIsSuccessful($code = HttpCode::OK)
    {
        $this->seeResponseIsJson();
        $this->seeResponseCodeIs($code);
    }

    /**
     * Checks if the response was successful
     */
    public function seeResponseIs400()
    {
        $this->checkErrorResponse(HttpCode::BAD_REQUEST);
    }

    /**
     * Checks if the response was successful
     */
    public function seeResponseIs404()
    {
        $this->checkErrorResponse(HttpCode::NOT_FOUND);
    }

    /**
     * Is a error response
     *
     * @param string $message
     * @return void
     */
    public function seeErrorJsonResponse(string $message)
    {
        $this->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'errors' => [
                    'message' => $message,
                ],
            ]
        );
    }

    /**
     * Is a successful json response
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function seeSuccessJsonResponse(string $key = 'data', array $data = [])
    {
        $this->seeResponseContainsJson([$key => $data]);
    }

    /**
     * Login the user
     *
     * @return void
     */
    public function apiLogin() : object
    {
        $this->deleteHeader('Authorization');
        $this->sendPOST(DataPage::$loginUrl, DataPage::loginJson());
        $this->seeResponseIsSuccessful();

        $response = $this->grabResponse();

        return json_decode($response);
    }

    /**
     * Check if it has hash
     *
     * @return void
     */
    private function checkHash()
    {
        $response = $this->grabResponse();
        $response = json_decode($response, true);
        $timestamp = $response['meta']['timestamp'];
        $hash = $response['meta']['hash'];
        unset($response['meta'], $response['jsonapi']);
        $this->assertEquals($hash, sha1($timestamp . json_encode($response)));
    }

    /**
     * Check if it has a error
     *
     * @param integer $code
     * @return void
     */
    private function checkErrorResponse(int $code)
    {
        $response = $this->grabResponse();

        $response = json_decode($response, true);

        $timestamp = $response['meta']['timestamp'];
        $hash = $response['meta']['hash'];
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
                'errors' => [
                    'message' => 'string'
                ],
                'meta' => [
                    'timestamp' => 'string:date',
                    'hash' => 'string',
                ]
            ]
        );

        $this->checkHash();
    }
}

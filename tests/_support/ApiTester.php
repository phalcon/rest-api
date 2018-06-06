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
                'data'    => 'array',
                'errors'  => [
                    'code'   => 'integer',
                    'detail' => 'string',
                ],
                'meta'    => [
                    'timestamp' => 'string:date',
                    'hash'      => 'string',
                ]
            ]
        );

        $response  = $this->grabResponse();
        $response  = json_decode($response, true);
        $data      = json_encode($response['data']);
        $timestamp = $response['meta']['timestamp'];
        $hash      = $response['meta']['hash'];
        $this->assertEquals($hash, sha1($timestamp . $data));
    }

    public function seeErrorJsonResponse(string $message)
    {
        $this->seeResponseContainsJson(
            [
                'jsonapi' => [
                    'version' => '1.0',
                ],
                'errors' => [
                    'code'   => Response::STATUS_ERROR,
                    'detail' => $message,
                ],
            ]
        );
    }

    public function seeSuccessJsonResponse(array $data = [])
    {
        $contents = [
            'jsonapi' => [
                'version' => '1.0',
            ],
            'errors' => [
                'code'   => Response::STATUS_SUCCESS,
                'detail' => '',
            ],
        ];

        if (true !== empty($da)) {
            $contents['data'] = $data;
        }

        $this->seeResponseContainsJson($contents);
    }

    public function apiLogin()
    {
        $this->deleteHeader('Authorization');
        $this->sendPOST(DataPage::$loginUrl, DataPage::loginJson());
        $this->seeResponseIsSuccessful();

        $record  = $this->getRecordWithFields(Users::class, ['usr_username' => 'testuser']);
        $dbToken = $record->get('usr_token_pre') . '.'
                 . $record->get('usr_token_mid') . '.'
                 . $record->get('usr_token_post');

        $response = $this->grabResponse();
        $response  = json_decode($response, true);
        $data      = $response['data'];
        $token     = $data['token'];
        $this->assertEquals($dbToken, $token);

        return $token;
    }
}

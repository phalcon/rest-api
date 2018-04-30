<?php

use Codeception\Util\HttpCode;
use Niden\Http\Response;

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
                    'source' => 'string',
                    'detail' => 'string',
                ],
                'meta'    => [
                    'timestamp' => 'string:date',
                    'hash'      => 'string',
                ]
            ]
        );
    }
}

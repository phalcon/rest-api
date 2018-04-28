<?php

namespace Niden\Tests\unit\library\Http;

use function is_string;
use function json_decode;
use Niden\Http\Response;
use \UnitTester;

class ResponseCest
{
    public function checkResponseWithStringPayload(UnitTester $I)
    {
        $response = new Response();

        $response
            ->setPayloadStatusSuccess()
            ->setPayloadContent('test');

        $contents = $response->getContent();
        $I->assertTrue(is_string($contents));

        $payload = $this->checkPayload($I, $response);

        $I->assertEquals(Response::STATUS_SUCCESS, $payload['errors']['code']);
        $I->assertEquals(['test'], $payload['data']);
    }

    public function checkResponseWithArrayPayload(UnitTester $I)
    {
        $response = new Response();

        $response
            ->setPayloadStatusSuccess()
            ->setPayloadContent(['a' => 'b']);

        $payload = $this->checkPayload($I, $response);

        $I->assertEquals(Response::STATUS_SUCCESS, $payload['errors']['code']);
        $I->assertEquals(['a' => 'b'], $payload['data']);
    }

    public function checkResponseWithErrorCode(UnitTester $I)
    {
        $response = new Response();

        $response
            ->setPayloadStatusError()
            ->setPayloadContent('error');

        $payload = $this->checkPayload($I, $response);

        $I->assertEquals(Response::STATUS_ERROR, $payload['errors']['code']);
        $I->assertEquals(['error'], $payload['data']);
    }

    private function checkPayload(UnitTester $I, Response $response): array
    {
        $contents = $response->getContent();
        $I->assertTrue(is_string($contents));

        $payload = json_decode($contents, true);
        $I->assertEquals(4, count($payload));
        $I->assertTrue(isset($payload['jsonapi']));
        $I->assertTrue(isset($payload['data']));
        $I->assertTrue(isset($payload['errors']));
        $I->assertTrue(isset($payload['meta']));

        return $payload;
    }
}

<?php

namespace Niden\Tests\unit\library\Http;

use Niden\Http\Response;
use Phalcon\Mvc\Model\Message as ModelMessage;
use Phalcon\Validation\Message as ValidationMessage;
use Phalcon\Validation\Message\Group as ValidationGroup;
use UnitTester;
use function is_string;
use function json_decode;

class ResponseCest
{
    public function checkResponseWithStringPayload(UnitTester $I)
    {
        $response = new Response();

        $response
            ->setPayloadSuccess('test');

        $contents = $response->getContent();
        $I->assertTrue(is_string($contents));

        $payload = $this->checkPayload($I, $response);

        $I->assertFalse(isset($payload['errors']));
        $I->assertEquals('test', $payload['data']);
    }

    private function checkPayload(UnitTester $I, Response $response, bool $error = false): array
    {
        $contents = $response->getContent();
        $I->assertTrue(is_string($contents));

        $payload = json_decode($contents, true);
        $I->assertEquals(3, count($payload));
        $I->assertTrue(isset($payload['jsonapi']));
        if (true === $error) {
            $I->assertTrue(isset($payload['errors']));
        } else {
            $I->assertTrue(isset($payload['data']));
        }
        $I->assertTrue(isset($payload['meta']));

        return $payload;
    }

    public function checkResponseWithArrayPayload(UnitTester $I)
    {
        $response = new Response();

        $response
            ->setPayloadSuccess(['a' => 'b']);

        $payload = $this->checkPayload($I, $response);

        $I->assertFalse(isset($payload['errors']));
        $I->assertEquals(['a' => 'b'], $payload['data']);
    }

    public function checkResponseWithErrorCode(UnitTester $I)
    {
        $response = new Response();

        $response
            ->setPayloadError('error content');

        $payload = $this->checkPayload($I, $response, true);

        $I->assertFalse(isset($payload['data']));
        $I->assertEquals('error content', $payload['errors'][0]);
    }

    public function checkResponseWithModelErrors(UnitTester $I)
    {
        $messages = [
            new ModelMessage('hello'),
            new ModelMessage('goodbye'),
        ];
        $response = new Response();
        $response
            ->setPayloadErrors($messages);

        $payload = $this->checkPayload($I, $response, true);

        $I->assertFalse(isset($payload['data']));
        $I->assertEquals(2, count($payload['errors']));
        $I->assertEquals('hello', $payload['errors'][0]);
        $I->assertEquals('goodbye', $payload['errors'][1]);
    }

    public function checkResponseWithValidationErrors(UnitTester $I)
    {
        $group   = new ValidationGroup();
        $message = new ValidationMessage('hello');
        $group->appendMessage($message);
        $message = new ValidationMessage('goodbye');
        $group->appendMessage($message);

        $response = new Response();
        $response
            ->setPayloadErrors($group);

        $payload = $this->checkPayload($I, $response, true);

        $I->assertFalse(isset($payload['data']));
        $I->assertEquals(2, count($payload['errors']));
        $I->assertEquals('hello', $payload['errors'][0]);
        $I->assertEquals('goodbye', $payload['errors'][1]);
    }
}

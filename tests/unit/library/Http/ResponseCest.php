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
        if (true === $error) {
            $I->assertTrue(isset($payload['errors']));
        } else {
            $I->assertTrue(isset($payload['data']));
        }

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

    public function checkHttpCodes(UnitTester $I)
    {
        $response = new Response();
        $I->assertEquals('200 (OK)', $response->getHttpCodeDescription($response::OK));
        $I->assertEquals('301 (Moved Permanently)', $response->getHttpCodeDescription($response::MOVED_PERMANENTLY));
        $I->assertEquals('302 (Found)', $response->getHttpCodeDescription($response::FOUND));
        $I->assertEquals('307 (Temporary Redirect)', $response->getHttpCodeDescription($response::TEMPORARY_REDIRECT));
        $I->assertEquals('308 (Permanent Redirect)', $response->getHttpCodeDescription($response::PERMANENTLY_REDIRECT));
        $I->assertEquals('400 (Bad Request)', $response->getHttpCodeDescription($response::BAD_REQUEST));
        $I->assertEquals('401 (Unauthorized)', $response->getHttpCodeDescription($response::UNAUTHORIZED));
        $I->assertEquals('403 (Forbidden)', $response->getHttpCodeDescription($response::FORBIDDEN));
        $I->assertEquals('404 (Not Found)', $response->getHttpCodeDescription($response::NOT_FOUND));
        $I->assertEquals('500 (Internal Server Error)', $response->getHttpCodeDescription($response::INTERNAL_SERVER_ERROR));
        $I->assertEquals('501 (Not Implemented)', $response->getHttpCodeDescription($response::NOT_IMPLEMENTED));
        $I->assertEquals('502 (Bad Gateway)', $response->getHttpCodeDescription($response::BAD_GATEWAY));
        $I->assertEquals(999, $response->getHttpCodeDescription(999));
    }
}

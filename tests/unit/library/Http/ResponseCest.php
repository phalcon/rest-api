<?php

namespace Phalcon\Api\Tests\unit\library\Http;

use Phalcon\Api\Http\Response;
use Phalcon\Messages\Message;
use Phalcon\Messages\Messages;
use UnitTester;

use function is_string;
use function json_decode;

class ResponseCest
{
    public function checkResponseWithStringPayload(UnitTester $I)
    {
        $response = new Response();

        $response
            ->setPayloadSuccess('test')
        ;

        $contents = $response->getContent();
        $I->assertTrue(is_string($contents));

        $payload = $this->checkPayload($I, $response);

        $I->assertFalse(isset($payload['errors']));
        $I->assertSame('test', $payload['data']);
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
            ->setPayloadSuccess(['a' => 'b'])
        ;

        $payload = $this->checkPayload($I, $response);

        $I->assertFalse(isset($payload['errors']));
        $I->assertSame(['a' => 'b'], $payload['data']);
    }

    public function checkResponseWithErrorCode(UnitTester $I)
    {
        $response = new Response();

        $response
            ->setPayloadError('error content')
        ;

        $payload = $this->checkPayload($I, $response, true);

        $I->assertFalse(isset($payload['data']));
        $I->assertSame('error content', $payload['errors'][0]);
    }

    public function checkResponseWithModelErrors(UnitTester $I)
    {
        $messages = [
            new Message('hello'),
            new Message('goodbye'),
        ];
        $response = new Response();
        $response
            ->setPayloadErrors($messages)
        ;

        $payload = $this->checkPayload($I, $response, true);

        $I->assertFalse(isset($payload['data']));
        $I->assertSame(2, count($payload['errors']));
        $I->assertSame('hello', $payload['errors'][0]);
        $I->assertSame('goodbye', $payload['errors'][1]);
    }

    public function checkResponseWithValidationErrors(UnitTester $I)
    {
        $group   = new Messages();
        $message = new Message('hello');
        $group->appendMessage($message);
        $message = new Message('goodbye');
        $group->appendMessage($message);

        $response = new Response();
        $response
            ->setPayloadErrors($group)
        ;

        $payload = $this->checkPayload($I, $response, true);

        $I->assertFalse(isset($payload['data']));
        $I->assertSame(2, count($payload['errors']));
        $I->assertSame('hello', $payload['errors'][0]);
        $I->assertSame('goodbye', $payload['errors'][1]);
    }

    public function checkHttpCodes(UnitTester $I)
    {
        $response = new Response();
        $I->assertSame('200 (OK)', $response->getHttpCodeDescription($response::OK));
        $I->assertSame('301 (Moved Permanently)', $response->getHttpCodeDescription($response::MOVED_PERMANENTLY));
        $I->assertSame('302 (Found)', $response->getHttpCodeDescription($response::FOUND));
        $I->assertSame('307 (Temporary Redirect)', $response->getHttpCodeDescription($response::TEMPORARY_REDIRECT));
        $I->assertSame('308 (Permanent Redirect)', $response->getHttpCodeDescription($response::PERMANENTLY_REDIRECT));
        $I->assertSame('400 (Bad Request)', $response->getHttpCodeDescription($response::BAD_REQUEST));
        $I->assertSame('401 (Unauthorized)', $response->getHttpCodeDescription($response::UNAUTHORIZED));
        $I->assertSame('403 (Forbidden)', $response->getHttpCodeDescription($response::FORBIDDEN));
        $I->assertSame('404 (Not Found)', $response->getHttpCodeDescription($response::NOT_FOUND));
        $I->assertSame('500 (Internal Server Error)', $response->getHttpCodeDescription($response::INTERNAL_SERVER_ERROR));
        $I->assertSame('501 (Not Implemented)', $response->getHttpCodeDescription($response::NOT_IMPLEMENTED));
        $I->assertSame('502 (Bad Gateway)', $response->getHttpCodeDescription($response::BAD_GATEWAY));
        $I->assertSame('999 (Undefined code)', $response->getHttpCodeDescription(999));
    }
}

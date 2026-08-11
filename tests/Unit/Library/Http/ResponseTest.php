<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Tests\Unit\Library\Http;

use Phalcon\Api\Http\Response;
use Phalcon\Messages\Message;
use Phalcon\Messages\Messages;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function is_string;
use function json_decode;
use function ob_end_clean;
use function ob_start;
use function sha1;

final class ResponseTest extends AbstractUnitTestCase
{
    public function testHttpCodes(): void
    {
        $response = new Response();
        $this->assertSame(
            '301 (Moved Permanently)',
            $response->getHttpCodeDescription($response::MOVED_PERMANENTLY)
        );
        $this->assertSame(
            '200 (OK)',
            $response->getHttpCodeDescription($response::OK)
        );
        $this->assertSame(
            '200 (OK)',
            $response->getHttpCodeDescription($response::OK)
        );
        $this->assertSame(
            '301 (Moved Permanently)',
            $response->getHttpCodeDescription($response::MOVED_PERMANENTLY)
        );
        $this->assertSame(
            '302 (Found)',
            $response->getHttpCodeDescription($response::FOUND)
        );
        $this->assertSame(
            '307 (Temporary Redirect)',
            $response->getHttpCodeDescription($response::TEMPORARY_REDIRECT)
        );
        $this->assertSame(
            '308 (Permanent Redirect)',
            $response->getHttpCodeDescription($response::PERMANENTLY_REDIRECT)
        );
        $this->assertSame(
            '400 (Bad Request)',
            $response->getHttpCodeDescription($response::BAD_REQUEST)
        );
        $this->assertSame(
            '401 (Unauthorized)',
            $response->getHttpCodeDescription($response::UNAUTHORIZED)
        );
        $this->assertSame(
            '403 (Forbidden)',
            $response->getHttpCodeDescription($response::FORBIDDEN)
        );
        $this->assertSame(
            '404 (Not Found)',
            $response->getHttpCodeDescription($response::NOT_FOUND)
        );
        $this->assertSame(
            '500 (Internal Server Error)',
            $response->getHttpCodeDescription($response::INTERNAL_SERVER_ERROR)
        );
        $this->assertSame('501 (Not Implemented)', $response->getHttpCodeDescription($response::NOT_IMPLEMENTED));
        $this->assertSame('502 (Bad Gateway)', $response->getHttpCodeDescription($response::BAD_GATEWAY));
        $this->assertSame('999 (Undefined code)', $response->getHttpCodeDescription(999));
    }

    public function testResponseWithArrayPayload(): void
    {
        $response = new Response();

        $response
            ->setPayloadSuccess(['a' => 'b'])
        ;

        $payload = $this->checkPayload($response);

        $this->assertFalse(isset($payload['errors']));
        $this->assertSame(['a' => 'b'], $payload['data']);
    }

    public function testResponseWithErrorCode(): void
    {
        $response = new Response();

        $response
            ->setPayloadError('error content')
        ;

        $payload = $this->checkPayload($response, true);

        $this->assertFalse(isset($payload['data']));
        $this->assertSame('error content', $payload['errors'][0]);
    }

    public function testResponseWithModelErrors(): void
    {
        $messages = [
            new Message('hello'),
            new Message('goodbye'),
        ];
        $response = new Response();
        $response
            ->setPayloadErrors($messages)
        ;

        $payload = $this->checkPayload($response, true);

        $this->assertFalse(isset($payload['data']));
        $this->assertSame(2, count($payload['errors']));
        $this->assertSame('hello', $payload['errors'][0]);
        $this->assertSame('goodbye', $payload['errors'][1]);
    }

    public function testResponseWithStringPayload(): void
    {
        $response = new Response();

        $response
            ->setPayloadSuccess('test')
        ;

        $contents = $response->getContent();
        $this->assertTrue(is_string($contents));

        $payload = $this->checkPayload($response);

        $this->assertFalse(isset($payload['errors']));
        $this->assertSame('test', $payload['data']);
    }

    public function testResponseWithValidationErrors(): void
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

        $payload = $this->checkPayload($response, true);

        $this->assertFalse(isset($payload['data']));
        $this->assertSame(2, count($payload['errors']));
        $this->assertSame('hello', $payload['errors'][0]);
        $this->assertSame('goodbye', $payload['errors'][1]);
    }

    public function testSendWrapsPayloadWithMetaAndHash(): void
    {
        $response = new Response();
        $response->setPayloadSuccess(['a' => 'b']);

        $original = $response->getContent();

        ob_start();
        $response->send();
        ob_end_clean();

        $payload = json_decode($response->getContent(), true);

        /**
         * The meta envelope and both of its members have to survive - dropping
         * either leaves clients without the timestamp or the integrity hash.
         */
        $this->assertArrayHasKey('meta', $payload);
        $this->assertArrayHasKey('timestamp', $payload['meta']);
        $this->assertArrayHasKey('hash', $payload['meta']);

        /**
         * The hash is sha1 over the timestamp followed by the original body, in
         * that order. Reversing the operands or dropping either one produces a
         * different digest, which is what the recomputation here pins down.
         */
        $this->assertSame(
            sha1($payload['meta']['timestamp'] . $original),
            $payload['meta']['hash']
        );

        $this->assertSame(sha1($original), $response->getHeaders()->get('E-Tag'));

        $this->assertSame('1.0', $payload['jsonapi']['version']);
        $this->assertSame(['a' => 'b'], $payload['data']);
    }

    /**
     * An input that already carries a `data` key must be sent as-is, not nested
     * under a second `data`. The two branches of setPayloadSuccess only diverge
     * for this shape, so it is what nails the is_array test down; a plain array
     * takes the same path either way.
     */
    public function testSetPayloadSuccessDoesNotDoubleWrapDataKey(): void
    {
        $response = new Response();
        $response->setPayloadSuccess(['data' => 'value']);

        $payload = $this->checkPayload($response);

        $this->assertSame('value', $payload['data']);
    }

    private function checkPayload(Response $response, bool $error = false): array
    {
        $contents = $response->getContent();
        $this->assertTrue(is_string($contents));

        $payload = json_decode($contents, true);
        if (true === $error) {
            $this->assertTrue(isset($payload['errors']));
        } else {
            $this->assertTrue(isset($payload['data']));
        }

        return $payload;
    }
}

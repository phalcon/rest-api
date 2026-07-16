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

namespace Phalcon\Api\Tests\Api;

use Phalcon\Api\Constants\Flags;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Talon\Http\HttpCode;
use Phalcon\Talon\Traits\RestAssertionsTrait;
use Phalcon\Talon\Traits\RestTrait;

use function json_decode;
use function json_encode;
use function sha1;

/**
 * Ported from the Codeception `ApiTester` actor.
 *
 * The suite pairs the application container with real HTTP: records are created
 * in-process through the models, while requests cross the network to the running
 * server. That is what `api.suite.yml` described as `Helper\Integration` plus
 * `REST: depends: PhpBrowser`, and it is why this extends the integration case
 * rather than Talon's AbstractRestTestCase - the record helpers and the REST
 * surface are both needed, and PHP allows only one parent.
 */
abstract class AbstractApiTestCase extends AbstractIntegrationTestCase
{
    use RestAssertionsTrait;
    use RestTrait;

    protected function addApiUserRecord(): Users
    {
        return $this->haveRecordWithFields(
            Users::class,
            [
                'status'        => Flags::ACTIVE,
                'username'      => Data::$testUsername,
                'password'      => Data::$testPasswordHash,
                'issuer'        => Data::$testIssuer,
                'tokenPassword' => Data::$testTokenPassword,
                'tokenId'       => Data::$testTokenId,
            ]
        );
    }

    protected function apiLogin(): string
    {
        $this->unsetHttpHeader('Authorization');
        $this->sendPost(Data::$loginUrl, Data::loginJson());
        $this->assertResponseIsSuccessful();

        $response = json_decode($this->grabResponse(), true);
        $data     = $response['data'] ?? [];

        return $data['token'] ?? '';
    }

    protected function assertErrorJsonResponse(string $message): void
    {
        $this->assertResponseContainsJson(
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

    protected function assertResponseIs400(): void
    {
        $this->assertErrorResponse(HttpCode::BAD_REQUEST);
    }

    protected function assertResponseIs404(): void
    {
        $this->assertErrorResponse(HttpCode::NOT_FOUND);
    }

    protected function assertResponseIsSuccessful(int $code = HttpCode::OK): void
    {
        $this->assertResponseIsJson();
        $this->assertResponseCodeIs($code);
        $this->assertResponseMatchesJsonType(
            [
                'jsonapi' => [
                    'version' => 'string',
                ],
                'meta'    => [
                    'timestamp' => 'string:date',
                    'hash'      => 'string',
                ],
            ]
        );

        $this->assertHash();
    }

    /**
     * @param array<array-key, mixed> $data
     */
    protected function assertSuccessJsonResponse(string $key = 'data', array $data = []): void
    {
        $this->assertResponseContainsJson([$key => $data]);
    }

    private function assertErrorResponse(int $code): void
    {
        $this->assertResponseIsJson();
        $this->assertResponseCodeIs($code);
        $this->assertResponseMatchesJsonType(
            [
                'jsonapi' => [
                    'version' => 'string',
                ],
                'meta'    => [
                    'timestamp' => 'string:date',
                    'hash'      => 'string',
                ],
            ]
        );

        $response = json_decode($this->grabResponse(), true);
        $this->assertSame(HttpCode::getDescription($code), $response['errors'][0]);

        $this->assertHash();
    }

    /**
     * The application signs each payload: sha1 over the timestamp followed by
     * the body with the `meta` and `jsonapi` envelopes removed.
     */
    private function assertHash(): void
    {
        $response  = json_decode($this->grabResponse(), true);
        $timestamp = $response['meta']['timestamp'];
        $hash      = $response['meta']['hash'];

        unset($response['meta'], $response['jsonapi']);

        $this->assertSame($hash, sha1($timestamp . json_encode($response)));
    }
}

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
use Phalcon\Api\Tests\Support\Data;

use function json_decode;

final class LoginTest extends AbstractApiTestCase
{
    public function testLoginKnownUser(): void
    {
        $this->haveRecordWithFields(
            Users::class,
            [
                'status'        => Flags::ACTIVE,
                'username'      => Data::$testUsername,
                'password'      => Data::$testPassword,
                'issuer'        => Data::$testIssuer,
                'tokenPassword' => Data::$strongPassphrase,
                'tokenId'       => Data::$testTokenId,
            ]
        );

        $this->sendPost(Data::$loginUrl, Data::loginJson());
        $this->assertResponseIsSuccessful();

        $response = json_decode($this->grabResponse(), true);

        $this->assertTrue(isset($response['data']));
        $this->assertTrue(isset($response['data']['token']));
        $this->assertTrue(isset($response['meta']));
    }

    public function testLoginUnknownUser(): void
    {
        $this->sendPost(
            Data::$loginUrl,
            [
                'username' => 'user',
                'password' => 'pass',
            ]
        );
        $this->assertResponseIsSuccessful();
        $this->assertErrorJsonResponse('Incorrect credentials');
    }
}

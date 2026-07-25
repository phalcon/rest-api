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

namespace Phalcon\Api\Tests\Api\Users;

use Phalcon\Api\Models\Users;
use Phalcon\Api\Services\TokenService;
use Phalcon\Api\Tests\Api\AbstractApiTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;

use function explode;
use function json_decode;
use function time;
use function usleep;

final class GetTest extends AbstractApiTestCase
{
    public function testGetManyUsers(): void
    {
        $userOne = $this->haveRecordWithFields(
            Users::class,
            [
                'status'        => 1,
                'username'      => Data::$testUsername,
                'password'      => Data::$testPasswordHash,
                'issuer'        => Data::$testIssuer,
                'tokenPassword' => Data::$strongPassphrase,
                'tokenId'       => Data::$testTokenId,
            ]
        );

        $userTwo = $this->haveRecordWithFields(
            Users::class,
            [
                'status'        => 1,
                'username'      => Data::$testUsername . '1',
                'password'      => Data::$testPassword . '1',
                'issuer'        => Data::$testIssuer . '1',
                'tokenPassword' => Data::$strongPassphrase . '1',
                'tokenId'       => Data::$testTokenId . '1',
            ]
        );

        $token = $this->apiLogin();

        $this->sendGetAs($token, Data::$usersUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::userResponse($userOne),
                Data::userResponse($userTwo),
            ]
        );
    }

    public function testGetManyUsersWithNoData(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->sendGetAs($token, Data::$usersUrl);
        $this->assertResponseIsSuccessful();
    }

    /**
     * The suite asserts JSON subsets, so extra attributes in a response pass
     * silently - which is how `password` and `tokenPassword` were published for
     * as long as they were. This asserts their absence explicitly.
     *
     * The stored password is a hash, so it is the hash that would leak - not
     * the plain password, which never reaches the database.
     */
    public function testGetUserDoesNotPublishSecrets(): void
    {
        $record = $this->addApiUserRecord();
        $token  = $this->apiLogin();

        $this->sendGetAs($token, Data::$usersUrl . '/' . $record->get('id'));
        $this->assertResponseIsSuccessful();

        $this->assertResponseNotContains('password');
        $this->assertResponseNotContains(Data::$testPasswordHash);
        $this->assertResponseNotContains(Data::$testTokenPassword);
    }

    public function testLoginKnownUserCorrectToken(): void
    {
        $this->addApiUserRecord();
        $this->apiLogin();
    }

    public function testLoginKnownUserExpiredToken(): void
    {
        $record = $this->addApiUserRecord();
        $this->unsetHttpHeader('Authorization');
        $this->sendPost(Data::$loginUrl, Data::loginJson());
        $this->assertResponseIsSuccessful();

        $signer  = new Hmac();
        $builder = new Builder($signer);

        $token = $builder
            ->setIssuer(Data::$testIssuer)
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpirationTime(time())
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        usleep(1000000);
        $expiredToken = $token->getToken();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $expiredToken);
        $this->sendGet(Data::$usersUrl . '/' . $record->get('id'));
        $this->assertResponseIsSuccessful();
        $this->assertErrorJsonResponse('Invalid Token (verification)');
    }

    public function testLoginKnownUserGetUnknownUser(): void
    {
        $record = $this->addApiUserRecord();
        $this->unsetHttpHeader('Authorization');
        $this->sendPost(Data::$loginUrl, Data::loginJson());
        $this->assertResponseIsSuccessful();

        $response = json_decode($this->grabResponse(), true);
        $token    = $response['data']['token'];

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        /**
         * This test creates a single user, which owns the lowest id, so the
         * next id up cannot exist. Asking for a fixed `1` only ever worked
         * while ids ran into the thousands; now that tearDown truncates and
         * they restart from one, `1` is the user we just made.
         */
        $this->sendGet(Data::$usersUrl . '/' . ($record->get('id') + 1));
        $this->assertResponseIs404();
    }

    public function testLoginKnownUserIncorrectSignature(): void
    {
        $record = $this->addApiUserRecord();
        $this->unsetHttpHeader('Authorization');
        $this->sendPost(Data::$loginUrl, Data::loginJson());
        $this->assertResponseIsSuccessful();

        $signer  = new Hmac();
        $builder = new Builder($signer);

        $token = $builder
            ->setIssuer(Data::$testIssuer)
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpirationTime(time() + 3000)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $wrongToken = $token->getToken();
        $parts      = explode('.', $wrongToken);
        $wrongToken = $parts[0] . '.' . $parts[1] . '.';

        $this->haveHttpHeader('Authorization', 'Bearer ' . $wrongToken);
        $this->sendGet(Data::$usersUrl . '/' . $record->get('id'));
        $this->assertResponseIsSuccessful();
        $this->assertErrorJsonResponse('Invalid Token (verification)');
    }

    public function testLoginKnownUserInvalidToken(): void
    {
        $record = $this->addApiUserRecord();
        $this->unsetHttpHeader('Authorization');
        $this->sendPost(Data::$loginUrl, Data::loginJson());
        $this->assertResponseIsSuccessful();

        $signer  = new Hmac();
        $builder = new Builder($signer);

        $token = $builder
            ->setIssuer(Data::$testIssuer)
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpirationTime(time() + 3000)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $invalidToken = $token->getToken() . 'xx';

        $this->haveHttpHeader('Authorization', 'Bearer ' . $invalidToken);
        $this->sendGet(Data::$usersUrl . '/' . $record->get('id'));
        $this->assertResponseIsSuccessful();
        $this->assertErrorJsonResponse('Invalid Token (verification)');
    }

    public function testLoginKnownUserInvalidUserInToken(): void
    {
        $record = $this->addApiUserRecord();
        $this->unsetHttpHeader('Authorization');
        $this->sendPost(Data::$loginUrl, Data::loginJson());
        $this->assertResponseIsSuccessful();

        $signer  = new Hmac();
        $builder = new Builder($signer);

        $token = $builder
            ->setIssuer(Data::$testIssuer)
            ->setAudience($this->getTokenAudience())
            ->setId(Data::$testTokenId)
            ->setIssuedAt(time() - 3600)
            ->setNotBefore(time() - 3590)
            ->setExpirationTime(time() + 3000)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
        ;

        $invalidToken = $token->getToken();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $invalidToken);
        $this->sendGet(Data::$usersUrl . '/' . $record->get('id'));
        $this->assertResponseIsSuccessful();
        $this->assertErrorJsonResponse('Invalid Token (verification)');
    }

    public function testLoginKnownUserNoToken(): void
    {
        $this->unsetHttpHeader('Authorization');
        $this->sendGet(Data::$usersUrl . '/1');
        $this->assertResponseIsSuccessful();
        $this->assertErrorJsonResponse('Invalid Token');
    }

    public function testLoginKnownUserValidToken(): void
    {
        $record = $this->addApiUserRecord();
        $token  = $this->apiLogin();

        $this->sendGetAs($token, Data::$usersUrl . '/' . $record->get('id'));
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::userResponse($record),
            ]
        );
    }

    private function getTokenAudience(): string
    {
        /** @var TokenService $service */
        $service = $this->grabFromDi('tokenService');

        return $service->getAudience();
    }
}

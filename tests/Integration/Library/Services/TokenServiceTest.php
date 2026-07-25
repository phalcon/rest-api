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

namespace Phalcon\Api\Tests\Integration\Library\Services;

use Phalcon\Api\Exception\TokenException;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Services\TokenService;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Tests\Support\Data;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Enum;

use function time;

final class TokenServiceTest extends AbstractIntegrationTestCase
{
    /**
     * A token that names a user we do not have is rejected at phase 1.
     */
    public function testAuthenticateRejectsUnknownUser(): void
    {
        $service = $this->getTokenService();

        $token = (new Builder(new Hmac()))
            ->setIssuer(Data::$testIssuer)
            ->setAudience($service->getAudience())
            ->setId('no-such-token-id')
            ->setExpirationTime(time() + 10)
            ->setPassphrase(Data::$strongPassphrase)
            ->getToken()
            ->getToken()
        ;

        $this->expectException(TokenException::class);
        $this->expectExceptionMessage('Invalid token (user)');

        $service->authenticate($token);
    }

    /**
     * The user is found, but the signature was made with another passphrase -
     * rejected at phase 2.
     */
    public function testAuthenticateRejectsWrongPassphrase(): void
    {
        $service = $this->getTokenService();
        $this->haveUser();

        $token = (new Builder(new Hmac()))
            ->setIssuer(Data::$testIssuer)
            ->setAudience($service->getAudience())
            ->setId(Data::$testTokenId)
            ->setExpirationTime(time() + 10)
            ->setPassphrase(Data::$strongPassphrase . '-wrong')
            ->getToken()
            ->getToken()
        ;

        $this->expectException(TokenException::class);
        $this->expectExceptionMessage('Invalid Token (verification)');

        $service->authenticate($token);
    }

    /**
     * Signature is good and the user exists, but the claims do not hold -
     * rejected at phase 3.
     */
    public function testAuthenticateRejectsWrongClaims(): void
    {
        $service = $this->getTokenService();
        $user    = $this->haveUser();

        $token = (new Builder(new Hmac()))
            ->setIssuer(Data::$testIssuer)
            ->setAudience('https://not-our-audience.example')
            ->setId(Data::$testTokenId)
            ->setExpirationTime(time() + 10)
            ->setPassphrase($user->get('tokenPassword'))
            ->getToken()
            ->getToken()
        ;

        $this->expectException(TokenException::class);

        $service->authenticate($token);
    }

    /**
     * The full round trip: a token this service issued is one it accepts, and
     * it answers with the record the token belongs to.
     */
    public function testIssuedTokenAuthenticates(): void
    {
        $service = $this->getTokenService();
        $user    = $this->haveUser();

        $actual = $service->authenticate($service->issue($user));

        $this->assertSame($user->get('id'), $actual->get('id'));
        $this->assertSame($user->get('username'), $actual->get('username'));
    }

    /**
     * issue() builds and signs the token from the record, which is what the
     * login endpoint hands back.
     */
    public function testIssueBuildsTheToken(): void
    {
        $service = $this->getTokenService();
        $user    = $this->haveUser();

        $parsed = $service->parse($service->issue($user));
        $claims = $parsed->getClaims();

        $this->assertSame(Data::$testIssuer, $claims->get(Enum::ISSUER));
        $this->assertSame(Data::$testTokenId, $claims->get(Enum::ID));
        $this->assertSame([$service->getAudience()], $claims->get(Enum::AUDIENCE));

        /**
         * The signature must verify against the record's passphrase - that is
         * the whole point of the token.
         *
         * Asserted against get('tokenPassword') rather than the raw constant:
         * get() sanitises, so a passphrase containing '&' comes back as
         * '&amp;'. Signing and verification both read through get(), so the key
         * in use is the sanitised form, not the string handed to set().
         */
        $passphrase = $user->get('tokenPassword');

        $this->assertTrue($parsed->verify(new Hmac(), $passphrase));
        $this->assertFalse($parsed->verify(new Hmac(), 'not-the-passphrase'));

        /**
         * Issued in the past, expiring in the future.
         */
        $now = time();
        $this->assertLessThanOrEqual($now, $claims->get(Enum::ISSUED_AT));
        $this->assertLessThanOrEqual($now, $claims->get(Enum::NOT_BEFORE));
        $this->assertGreaterThan($now, $claims->get(Enum::EXPIRATION_TIME));
    }

    /**
     * @return TokenService
     */
    private function getTokenService(): TokenService
    {
        /** @var TokenService $service */
        $service = $this->grabFromDi('tokenService');

        return $service;
    }

    /**
     * @return Users
     */
    private function haveUser(): Users
    {
        /** @var Users $user */
        $user = $this->haveRecordWithFields(
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

        return $user;
    }
}

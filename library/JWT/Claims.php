<?php

namespace Niden\JWT;

class Claims
{
    const JWT_AUDIENCE        = 'aud';
    const JWT_CIPHER       = 'alg';
    const JWT_CURVE           = 'crv';
    const JWT_EXPIRATION_TIME = 'exp';
    const JWT_ID              = 'jti';
    const JWT_ISSUED_AT       = 'iat';
    const JWT_ISSUER          = 'iss';
    const JWT_KEY_TYPE        = 'kty';
    const JWT_NOT_BEFORE      = 'nbf';
    const JWT_SUBJECT         = 'sub';

    const JWT_KEY_TYPE_EC    = 'EC';
    const JWT_KEY_TYPE_RSA   = 'RSA';
    const JWT_KEY_TYPE_OCTET = 'oct';

    const JWT_CIPHER_HS256 = 'HS256';
    const JWT_CIPHER_HS384 = 'HS384';
    const JWT_CIPHER_HS512 = 'HS512';
    const JWT_CIPHER_RS256 = 'RS256';
    const JWT_CIPHER_RS384 = 'RS384';
    const JWT_CIPHER_RS512 = 'RS512';
    const JWT_CIPHER_NONE  = 'none';

    const JWT_CIPHERS = [
        self::JWT_CIPHER_HS256 => ['hmac', 'SHA256'],
        self::JWT_CIPHER_HS384 => ['hmac', 'SHA384'],
        self::JWT_CIPHER_HS512 => ['hmac', 'SHA512'],

        self::JWT_CIPHER_RS256 => ['openssl', 'SHA256'],
        self::JWT_CIPHER_RS384 => ['openssl', 'SHA384'],
        self::JWT_CIPHER_RS512 => ['openssl', 'SHA512'],
        self::JWT_CIPHER_NONE  => ['none', ''],

        /**
         * These come from the definition document.
         *
         * ES256 | ECDSA using P-256 and SHA-256                  | Recommended+|
         * ES384 | ECDSA using P-384 and SHA-384                  | Optional    |
         * ES512 | ECDSA using P-521 and SHA-512                  | Optional    |
         * PS256 | RSASSA-PSS using SHA-256 and MGF1 with SHA-256 | Optional    |
         * PS384 | RSASSA-PSS using SHA-384 and MGF1 with SHA-384 | Optional    |
         * PS512 | RSASSA-PSS using SHA-512 and MGF1 with SHA-512 | Optional    |
         */
    ];
}

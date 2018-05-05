<?php

namespace Niden\JWT;

class Claims
{
    const JWT_ISSUER          = 'iss';
    const JWT_SUBJECT         = 'sub';
    const JWT_AUDIENCE        = 'aud';
    const JWT_EXPIRATION_TIME = 'exp';
    const JWT_NOT_BEFORE      = 'nbf';
    const JWT_ISSUED_AT       = 'iat';
    const JWT_ID              = 'jti';

    const JWT_ALGORITHM_HS256 = 'HS256';
    const JWT_ALGORITHM_HS384 = 'HS384';
    const JWT_ALGORITHM_HS512 = 'HS512';
    const JWT_ALGORITHM_RS256 = 'RS256';
    const JWT_ALGORITHM_RS384 = 'RS384';
    const JWT_ALGORITHM_RS512 = 'RS512';
    const JWT_ALGORITHM_NONE  = 'none';

    const JWT_ALGORITHMS = [
        self::JWT_ALGORITHM_HS256 => ['hash_hmac', 'SHA256'],
        self::JWT_ALGORITHM_HS384 => ['hash_hmac', 'SHA384'],
        self::JWT_ALGORITHM_HS512 => ['hash_hmac', 'SHA512'],

        self::JWT_ALGORITHM_RS256 => ['openssl', 'SHA256'],
        self::JWT_ALGORITHM_RS384 => ['openssl', 'SHA384'],
        self::JWT_ALGORITHM_RS512 => ['openssl', 'SHA512'],
        self::JWT_ALGORITHM_NONE  => [],

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

<?php

namespace Niden;

/**
 * Class JWTClaims
 */
class JWTClaims
{
    const JWT_ISSUER     = 'iss';
    const JWT_SUBKECT    = 'sub';
    const JWT_AUDIENCE   = 'aud';
    const JWT_EXPIRATION = 'exp';
    const JWT_NOT_BEFORE = 'nbf';
    const JWT_ISSUED_AT  = 'iat';
    const JWT_ID         = 'jti';
}

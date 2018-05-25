<?php

namespace Niden\Token;

use Lcobucci\JWT\ValidationData;
use Niden\Models\Users;

/**
 * Class TokenParser
 *
 * @package Niden
 */
class Validation
{
    /**
     * @param Users $user
     *
     * @return ValidationData
     * @throws \Niden\Exception\ModelException
     */
    public function get(Users $user)
    {
        $validationData = new ValidationData();
        $validationData->setIssuer('https://phalconphp.com');
        $validationData->setAudience($user->get('usr_domain_name'));
        $validationData->setId($user->get('usr_token_id'));

        return $validationData;
    }
}

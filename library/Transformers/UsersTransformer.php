<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\TransformerAbstract;
use Niden\Models\Users;

/**
 * Class UserTransformer
 */
class UsersTransformer extends TransformerAbstract
{
    /**
     * @param Users $user
     *
     * @return array
     * @throws \Niden\Exception\ModelException
     */
    public function transform(Users $user)
    {
        return [
            'id'            => $user->get('usr_id'),
            'status'        => $user->get('usr_status_flag'),
            'username'      => $user->get('usr_username'),
            'issuer'        => $user->get('usr_issuer'),
            'tokenPassword' => $user->get('usr_token_password'),
            'tokenId'       => $user->get('usr_token_id'),
        ];
    }
}
